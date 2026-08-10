<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Service;
use App\Support\MediaPath;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Brings legacy and external images onto the uploads disk so they can be
 * managed through Filament. Non-destructive: originals under public/images
 * are left in place, and anything that cannot be fetched is reported and
 * left untouched in the database.
 */
class MediaImportCommand extends Command
{
    protected $signature = 'media:import {--skip-remote : Do not attempt to download external URLs}';

    protected $description = 'Copy legacy and external content images onto the uploads disk';

    /** @var array<int, string> */
    private array $failures = [];

    public function handle(): int
    {
        foreach (Service::all() as $service) {
            $this->importModel($service, 'services/'.$service->slug);
        }

        foreach (Project::all() as $project) {
            $this->importModel($project, 'projects/'.$project->getKey());
        }

        if ($this->failures === []) {
            $this->info('All content images are on the uploads disk.');

            return self::SUCCESS;
        }

        $this->warn('Could not import '.count($this->failures).' image(s). Re-upload these through the admin panel:');

        foreach ($this->failures as $failure) {
            $this->line('  - '.$failure);
        }

        return self::SUCCESS;
    }

    private function importModel(Model $model, string $directory): void
    {
        $dirty = false;

        $image = $this->importValue($model->image, $directory, $model);
        if ($image !== $model->image) {
            $model->image = $image;
            $dirty = true;
        }

        if (is_array($model->gallery)) {
            $gallery = array_map(
                fn ($entry) => is_string($entry) ? $this->importValue($entry, $directory, $model) : $entry,
                $model->gallery
            );

            if ($gallery !== $model->gallery) {
                $model->gallery = $gallery;
                $dirty = true;
            }
        }

        if ($dirty) {
            $model->save();
        }
    }

    /**
     * Returns the new disk-relative value, or the original value unchanged if
     * the file could not be brought across.
     */
    private function importValue(?string $value, string $directory, Model $model): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // Already on the uploads disk.
        if (! MediaPath::isExternal($value) && ! Str::startsWith($value, '/')) {
            return $value;
        }

        if (MediaPath::isExternal($value)) {
            return $this->option('skip-remote')
                ? $this->recordFailure($value, $model)
                : $this->importRemote($value, $directory, $model);
        }

        return $this->importLocal($value, $directory, $model);
    }

    private function importLocal(string $value, string $directory, Model $model): string
    {
        $source = public_path(ltrim($value, '/'));

        if (! is_file($source)) {
            return $this->recordFailure($value, $model);
        }

        $basename = basename($source);
        $target = public_path('uploads/'.$directory.'/'.$basename);
        File::ensureDirectoryExists(dirname($target));

        if (! is_file($target)) {
            File::copy($source, $target);
        }

        // Bring the pre-generated thumbnail across too, if there is one.
        $thumbSource = preg_replace('/(\.jpe?g|\.png|\.webp)$/i', '-thumb$1', $source);
        if (is_string($thumbSource) && is_file($thumbSource)) {
            $thumbTarget = public_path('uploads/'.$directory.'/'.basename($thumbSource));
            if (! is_file($thumbTarget)) {
                File::copy($thumbSource, $thumbTarget);
            }
        }

        return $directory.'/'.$basename;
    }

    private function importRemote(string $value, string $directory, Model $model): string
    {
        try {
            $response = Http::timeout(20)->get($value);
        } catch (\Throwable) {
            return $this->recordFailure($value, $model);
        }

        if (! $response->successful()) {
            return $this->recordFailure($value, $model);
        }

        $extension = match (true) {
            str_contains($response->header('Content-Type') ?? '', 'png') => 'png',
            str_contains($response->header('Content-Type') ?? '', 'webp') => 'webp',
            default => 'jpg',
        };

        $basename = Str::slug(Str::limit(pathinfo(parse_url($value, PHP_URL_PATH) ?: 'image', PATHINFO_FILENAME), 40, '')).'-'.Str::random(6).'.'.$extension;
        $target = public_path('uploads/'.$directory.'/'.$basename);
        File::ensureDirectoryExists(dirname($target));
        File::put($target, $response->body());

        return $directory.'/'.$basename;
    }

    private function recordFailure(string $value, Model $model): string
    {
        $label = class_basename($model).' #'.$model->getKey().' → '.$value;

        if (! in_array($label, $this->failures, true)) {
            $this->failures[] = $label;
        }

        return $value;
    }
}
