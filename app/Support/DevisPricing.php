<?php

namespace App\Support;

/**
 * Pricing rules for the "devis" (quote) documents.
 *
 * Joinery is quoted per square metre: a line records its height and width and
 * points at one of the rates defined on the devis. A rate is a price per m²
 * plus an optional flat supplement charged once per unit — that supplement is
 * how a shutter rate carries its motor price ("Prix Moteur : 150dt").
 *
 * The same helpers back the Filament form's live preview and the persisted
 * values, so what the admin sees and what is stored cannot drift apart.
 */
class DevisPricing
{
    /**
     * Rates pre-loaded into a new devis — the joinery case the workshop
     * quotes most often.
     */
    public const DEFAULT_RATES = [
        [
            'label' => 'Aluminium',
            'price' => 600,
            'supplement' => 0,
            'supplement_label' => null,
        ],
        [
            'label' => 'Volet',
            'price' => 200,
            'supplement' => 150,
            'supplement_label' => 'Prix Moteur',
        ],
    ];

    /**
     * Money is stored with 2 decimals like the rest of the app, but Tunisian
     * amounts are written with 3 (millimes) on the printed documents.
     */
    public const DISPLAY_DECIMALS = 3;

    /**
     * Coerce whatever the form/database hands us into a predictable list of
     * rates. Unlabelled rows are dropped: a line can only reference a label.
     *
     * @return array<int, array{label: string, price: float, supplement: float, supplement_label: ?string}>
     */
    public static function normalizeRates(mixed $rates): array
    {
        if (! is_array($rates)) {
            return [];
        }

        $normalized = [];

        foreach ($rates as $rate) {
            if (! is_array($rate)) {
                continue;
            }

            $label = trim((string) ($rate['label'] ?? ''));

            if ($label === '') {
                continue;
            }

            $supplement = round((float) ($rate['supplement'] ?? 0), 2);
            $supplementLabel = trim((string) ($rate['supplement_label'] ?? ''));

            $normalized[$label] = [
                'label' => $label,
                'price' => round((float) ($rate['price'] ?? 0), 2),
                'supplement' => $supplement,
                'supplement_label' => $supplementLabel !== '' ? $supplementLabel : null,
            ];
        }

        // Keyed by label while building so a duplicated label collapses to one
        // rate instead of leaving an unreachable twin behind.
        return array_values($normalized);
    }

    /**
     * @return array{label: string, price: float, supplement: float, supplement_label: ?string}|null
     */
    public static function rateFor(mixed $rates, ?string $label): ?array
    {
        if ($label === null || trim($label) === '') {
            return null;
        }

        foreach (self::normalizeRates($rates) as $rate) {
            if ($rate['label'] === trim($label)) {
                return $rate;
            }
        }

        return null;
    }

    /**
     * Select options for the rate pickers on each devis line.
     *
     * @return array<string, string>
     */
    public static function rateOptions(mixed $rates): array
    {
        $options = [];

        foreach (self::normalizeRates($rates) as $rate) {
            $label = $rate['label'].' — '.self::format($rate['price']).' dt/m²';

            if ($rate['supplement'] > 0) {
                $label .= ' + '.self::format($rate['supplement']).' dt';
            }

            $options[$rate['label']] = $label;
        }

        return $options;
    }

    public static function area(mixed $height, mixed $width): float
    {
        return round((float) $height * (float) $width, 4);
    }

    /**
     * Price of one unit of a line: its surface at the rate's m² price, plus the
     * rate's flat supplement. Returns null when the line has no usable rate, so
     * callers can tell "no rate selected" apart from "costs nothing".
     */
    public static function unitPrice(mixed $rates, ?string $rateLabel, mixed $height, mixed $width): ?float
    {
        $rate = self::rateFor($rates, $rateLabel);

        if ($rate === null) {
            return null;
        }

        return round(self::area($height, $width) * $rate['price'] + $rate['supplement'], 2);
    }

    public static function lineTotal(mixed $unitPrice, mixed $shutterPrice, mixed $quantity): float
    {
        return round(((float) $unitPrice + (float) $shutterPrice) * (float) $quantity, 2);
    }

    /**
     * Printable rate legend, matching the box under the table on the paper
     * devis. A supplement gets its own line because that is how it is written
     * there ("Prix M² volet : 200dt" then "Prix Moteur : 150dt").
     *
     * @return array<int, array{label: string, price: float}>
     */
    public static function legendLines(mixed $rates): array
    {
        $lines = [];

        foreach (self::normalizeRates($rates) as $rate) {
            $lines[] = [
                'label' => 'Prix M² '.$rate['label'],
                'price' => $rate['price'],
            ];

            if ($rate['supplement'] > 0) {
                $lines[] = [
                    'label' => $rate['supplement_label'] ?? 'Supplément '.$rate['label'],
                    'price' => $rate['supplement'],
                ];
            }
        }

        return $lines;
    }

    /**
     * Amounts are written plainly — "14318.000", no thousands separator — the
     * way the workshop's existing devis do.
     */
    public static function format(mixed $value): string
    {
        return number_format((float) $value, self::DISPLAY_DECIMALS, '.', '');
    }

    /**
     * Dimensions keep 2 decimals: they are metres, written "1.41".
     */
    public static function formatDimension(mixed $value): string
    {
        if ($value === null || $value === '' || (float) $value == 0.0) {
            return '';
        }

        return number_format((float) $value, 2, '.', '');
    }
}
