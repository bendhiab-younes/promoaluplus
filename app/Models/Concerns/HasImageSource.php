<?php

namespace App\Models\Concerns;

use App\Support\MediaPath;

/**
 * Pairs an uploaded-file column with an external-URL fallback column so no
 * stored image is ever unreachable from the Filament form.
 */
trait HasImageSource
{
    public function imageSrc(): ?string
    {
        $value = filled($this->image) ? $this->image : $this->image_url;

        return MediaPath::url($value);
    }
}
