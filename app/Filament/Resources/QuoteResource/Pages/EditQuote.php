<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Télécharger PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->url(fn () => route('quote.pdf', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->items->count() > 0),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->calculateTotals();
    }
}
