<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Resolves the three image-path shapes that coexist in this database:
 *
 *   1. External absolute URLs   — "https://images.unsplash.com/..."
 *   2. Legacy root-relative     — "/images/services/doors/doors-01.jpeg"
 *   3. Uploads-disk relative    — "services/doors/doors-01.jpeg"
 *
 * Only shape 3 is produced by new admin uploads; the other two predate the
 * admin panel and are migrated opportunistically by `php artisan media:import`.
 */
class MediaPath
{
    public static function url(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (self::isExternal($value) || Str::startsWith($value, '/')) {
            return $value;
        }

        return asset('uploads/'.ltrim($value, '/'));
    }

    /**
     * The "-thumb" sibling if it actually exists on disk, otherwise the full
     * image. Legacy service images ship with pre-generated thumbnails; admin
     * uploads do not, and must never produce a 404.
     */
    public static function thumb(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || self::isExternal($value)) {
            return self::url($value === '' ? null : $value);
        }

        $thumbValue = preg_replace('/(\.jpe?g|\.png|\.webp)$/i', '-thumb$1', $value);

        if ($thumbValue === null || $thumbValue === $value) {
            return self::url($value);
        }

        return self::exists($thumbValue) ? self::url($thumbValue) : self::url($value);
    }

    public static function isExternal(string $value): bool
    {
        return Str::startsWith($value, ['http://', 'https://', '//']);
    }

    /**
     * Absolute filesystem check. Root-relative values live under public/,
     * disk-relative values under public/uploads/.
     */
    public static function exists(string $value): bool
    {
        if (self::isExternal($value)) {
            return false;
        }

        $path = Str::startsWith($value, '/')
            ? public_path(ltrim($value, '/'))
            : public_path('uploads/'.$value);

        return is_file($path);
    }
}
