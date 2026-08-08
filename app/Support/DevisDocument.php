<?php

namespace App\Support;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\SiteSetting;

/**
 * Assembles everything a devis document needs, so the PDF and the Excel export
 * describe the same document instead of each deciding for itself what a devis
 * looks like.
 */
class DevisDocument
{
    private function __construct(
        public readonly Quote $quote,
    ) {}

    public static function for(Quote $quote): self
    {
        $quote->loadMissing('items');

        return new self($quote);
    }

    /**
     * @return array{name: string, address: string, phone: string, email: string, tax_id: string}
     */
    public function company(): array
    {
        return [
            'name' => SiteSetting::get('company_name', 'Promo Alu Plus'),
            'address' => SiteSetting::get('contact_address', 'Sousse'),
            'phone' => SiteSetting::get('contact_phone', '26 192 898'),
            'email' => SiteSetting::get('contact_email', 'promoaluplus@gmail.com'),
            'tax_id' => SiteSetting::get('company_tax_id', '1901901B'),
        ];
    }

    /**
     * @return array{name: string, address: ?string, phone: ?string, email: ?string}
     */
    public function client(): array
    {
        $location = collect([$this->quote->client_address, $this->quote->city, $this->quote->country])
            ->filter()
            ->implode(', ');

        return [
            'name' => $this->quote->full_name,
            'address' => $location !== '' ? $location : null,
            'phone' => $this->quote->phone,
            'email' => $this->quote->email,
        ];
    }

    /**
     * Shutter pricing splits the table into P.VOLET / P.ALU columns; without it
     * the devis keeps the plainer single-price layout.
     */
    public function hasShutters(): bool
    {
        return $this->quote->items->contains(fn (QuoteItem $item): bool => $item->hasShutter());
    }

    /**
     * Column headings, in print order. The dimension columns swap around
     * because that is how each of the two devis formats is written.
     *
     * @return array<int, string>
     */
    public function columns(): array
    {
        return $this->hasShutters()
            ? ['Designation', 'H', 'L', 'Qté', 'P.VOLET', 'P.ALU', 'PT.HTVA']
            : ['Designation', 'L', 'H', 'Qté', 'PU.HTVA', 'PT.HTVA'];
    }

    /**
     * @return array<int, array{item: QuoteItem, designation: string, height: float, width: float, quantity: float, shutter_price: float, unit_price: float, total: float}>
     */
    public function rows(): array
    {
        return $this->quote->items
            ->map(fn (QuoteItem $item): array => [
                'item' => $item,
                'designation' => (string) $item->description,
                'height' => (float) $item->height,
                'width' => (float) $item->width,
                'quantity' => (float) $item->quantity,
                'shutter_price' => (float) $item->shutter_price,
                'unit_price' => (float) $item->unit_price,
                'total' => (float) $item->total,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, price: float}>
     */
    public function legend(): array
    {
        return $this->quote->rateLegend();
    }

    /**
     * @return array{subtotal: float, discount: float, show_tax: bool, tax_rate: float, tax: float, total: float}
     */
    public function totals(): array
    {
        $subtotal = (float) $this->quote->items->sum('total');
        $discount = (float) ($this->quote->discount ?? 0);
        $showTax = (bool) $this->quote->show_tax;
        $taxRate = $showTax ? (float) ($this->quote->tax_rate ?? 19) : 0.0;
        $tax = round(($subtotal - $discount) * ($taxRate / 100), 2);

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'show_tax' => $showTax,
            'tax_rate' => $taxRate,
            'tax' => $tax,
            'total' => round($subtotal - $discount + $tax, 2),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function notes(): array
    {
        return $this->quote->productNoteLines();
    }

    public function date(): string
    {
        return $this->quote->documentDate()->format('Y/m/d');
    }

    public function reference(): ?string
    {
        return $this->quote->quote_number;
    }

    /**
     * Width of the cached print logo. The source asset is a 1024px square
     * meant for the web; embedding it whole makes every devis a ~1.9 MB
     * download and takes DomPDF seconds to render.
     */
    private const LOGO_PRINT_WIDTH = 420;

    /**
     * Absolute path to the logo, or null when the asset is missing so the
     * documents degrade to a text header instead of failing to render.
     */
    public function logoPath(): ?string
    {
        $source = public_path('images/promo-alu-plus-logo.png');

        if (! is_file($source)) {
            return null;
        }

        return $this->printLogoPath($source) ?? $source;
    }

    /**
     * A downscaled copy of the logo, generated once and reused until the
     * source asset changes. Returns null if GD cannot produce it, in which
     * case the caller falls back to the original.
     *
     * Written as a JPEG flattened onto white: the documents print on white
     * paper anyway, and DomPDF turns a transparent PNG into an image plus a
     * soft mask, which quadruples the size of every devis.
     */
    private function printLogoPath(string $source): ?string
    {
        $cached = storage_path('app/devis/logo-print.jpg');

        if (is_file($cached) && filemtime($cached) >= filemtime($source)) {
            return $cached;
        }

        if (! function_exists('imagecreatefrompng')) {
            return null;
        }

        $image = @imagecreatefrompng($source);

        if ($image === false) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $targetWidth = min($width, self::LOGO_PRINT_WIDTH);
        $targetHeight = (int) round($height * ($targetWidth / $width));

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagefilledrectangle($resized, 0, 0, $targetWidth, $targetHeight, imagecolorallocate($resized, 255, 255, 255));
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        if (! is_dir(dirname($cached))) {
            mkdir(dirname($cached), 0755, true);
        }

        $saved = imagejpeg($resized, $cached, 88);

        imagedestroy($image);
        imagedestroy($resized);

        return $saved ? $cached : null;
    }
}
