<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    private ?bool $hasQuoteItems = null;

    public function getTitle(): string
    {
        return $this->record->quote_number
            ? 'Devis '.$this->record->quote_number
            : 'Chiffrer la demande de '.$this->record->full_name;
    }

    public function getSubheading(): ?string
    {
        return QuoteResource::statusHint($this->record->status);
    }

    private function hasQuoteItems(): bool
    {
        if ($this->hasQuoteItems === null) {
            $this->hasQuoteItems = $this->record->items()->exists();
        }

        return $this->hasQuoteItems;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Voir le détail')
                ->icon('heroicon-m-eye')
                ->color('gray'),
            Actions\Action::make('download_pdf')
                ->label('Devis PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->url(fn () => route('quote.pdf', $this->record))
                ->openUrlInNewTab()
                ->disabled(fn () => ! $this->hasQuoteItems())
                ->tooltip(fn () => ! $this->hasQuoteItems() ? 'Ajoutez des lignes au devis d\'abord' : 'Télécharger le devis en PDF'),
            Actions\Action::make('download_excel')
                ->label('Devis Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(fn () => route('quote.excel', $this->record))
                ->openUrlInNewTab()
                ->disabled(fn () => ! $this->hasQuoteItems())
                ->tooltip(fn () => ! $this->hasQuoteItems() ? 'Ajoutez des lignes au devis d\'abord' : 'Télécharger le devis en Excel (formules incluses)'),
            Actions\DeleteAction::make()->label('Supprimer'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Devis enregistré';
    }

    protected function afterSave(): void
    {
        $this->record->calculateTotals();
        $this->hasQuoteItems = null;
    }
}
