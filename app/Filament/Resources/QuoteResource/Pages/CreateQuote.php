<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    public function getTitle(): string
    {
        return 'Nouveau devis';
    }

    public function getSubheading(): ?string
    {
        return 'Renseignez le client, ajustez les tarifs au m², puis ajoutez les lignes — les prix se calculent tout seuls.';
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Devis créé';
    }

    protected function afterCreate(): void
    {
        $this->record->calculateTotals();
    }

    /**
     * Land on the builder rather than the list: a devis is almost never
     * finished in one pass, and the lines are usually the next thing to type.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
