<?php

namespace App\Providers;

use Filament\Forms\Components\FileUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Image formats an admin may upload. Deliberately excludes SVG: it is an
     * active-content format, and everything under public/uploads is served
     * same-origin, so a stored SVG is a stored XSS.
     *
     * @var list<string>
     */
    private const ALLOWED_UPLOAD_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];

    private const ALLOWED_UPLOAD_MIMETYPES = 'image/jpeg,image/png,image/webp,image/avif,image/gif';

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->hardenFileUploads();
    }

    /**
     * Applied centrally rather than on each of the eight FileUpload fields, so
     * a future upload field cannot be added without it.
     *
     * Filament's default storage name is `Str::ulid() . '.' . $file->getClientOriginalExtension()`
     * — the *client's* extension, kept verbatim. `->image()` only validates the
     * sniffed MIME type, so a valid GIF carrying a PHP payload and named
     * "x.php" passed validation and was stored as "<ulid>.php" inside
     * public/uploads. Any web server configured to run PHP by extension then
     * executes it: admin-panel access became remote code execution.
     *
     * Both halves are closed here — the extension is derived from the file's
     * actual content, and the accepted types are narrowed to real raster
     * images.
     */
    private function hardenFileUploads(): void
    {
        // isImportant: true is load-bearing. ComponentManager::configure() runs
        // ordinary callbacks *before* the component's own setUp(), and
        // BaseFileUpload::setUp() installs the vulnerable default filename
        // callback — so a normal registration here would be silently
        // overwritten. Important callbacks run after setUp() and win.
        FileUpload::configureUsing(function (FileUpload $upload): void {
            $upload
                ->rule('mimetypes:'.self::ALLOWED_UPLOAD_MIMETYPES)
                ->getUploadedFileNameForStorageUsing(static function (UploadedFile $file): string {
                    $extension = strtolower((string) $file->guessExtension());

                    // Validation should already have rejected anything else;
                    // this makes the stored name safe even if it did not.
                    if (! in_array($extension, self::ALLOWED_UPLOAD_EXTENSIONS, true)) {
                        $extension = 'bin';
                    }

                    return Str::ulid().'.'.$extension;
                });
        }, isImportant: true);
    }
}
