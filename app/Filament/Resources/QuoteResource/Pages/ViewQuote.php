<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

/**
 * Read-only recap of a devis: everything the workshop needs to answer a client
 * on the phone, plus the pipeline actions, without entering the builder form.
 */
class ViewQuote extends ViewRecord
{
    protected static string $resource = QuoteResource::class;

    public function getTitle(): string
    {
        return $this->record->quote_number
            ? 'Devis '.$this->record->quote_number
            : 'Demande de '.$this->record->full_name;
    }

    public function getSubheading(): ?string
    {
        return QuoteResource::statusHint($this->record->status);
    }

    /**
     * Re-read the devis so the recap reflects the transition that was just
     * applied instead of the state the page was rendered with.
     */
    protected function refreshRecord(): void
    {
        $this->record->refresh();
        $this->fillForm();
    }

    protected function hasQuoteItems(): bool
    {
        return $this->record->items()->exists();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label($this->hasQuoteItems() ? 'Modifier le devis' : 'Chiffrer le devis')
                ->icon($this->hasQuoteItems() ? 'heroicon-m-pencil-square' : 'heroicon-m-calculator'),

            Actions\Action::make('send_quote')
                ->label('Marquer comme envoyé')
                ->icon('heroicon-m-paper-airplane')
                ->color('primary')
                ->visible(fn (): bool => in_array($this->record->status, ['new', 'contacted'], true) && $this->hasQuoteItems())
                ->requiresConfirmation()
                ->modalHeading('Marquer le devis comme envoyé')
                ->modalDescription('Un numéro de devis sera attribué s\'il n\'en a pas encore.')
                ->action(function (): void {
                    $this->record->markAsQuoted();
                    $this->refreshRecord();
                    Notification::make()->success()->title('Devis envoyé')->body("Numéro attribué : {$this->record->quote_number}")->send();
                }),

            Actions\Action::make('mark_accepted')
                ->label('Accepté')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record->status === 'quoted')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->markAsAccepted();
                    $this->refreshRecord();
                    Notification::make()->success()->title('Devis accepté !')->send();
                }),

            Actions\Action::make('create_invoice')
                ->label('Créer la facture')
                ->icon('heroicon-m-banknotes')
                ->color('success')
                ->visible(fn (): bool => Invoice::moduleEnabled() && $this->record->status === 'accepted' && ! $this->record->invoice()->exists())
                ->requiresConfirmation()
                ->modalDescription('Une facture reprenant les lignes et les totaux de ce devis sera créée.')
                ->action(function (): void {
                    $invoice = $this->record->createInvoice();
                    $this->record->markAsCompleted();
                    $this->refreshRecord();
                    Notification::make()
                        ->success()
                        ->title('Facture créée')
                        ->body("Facture {$invoice->invoice_number} créée avec succès")
                        ->send();
                }),

            Actions\ActionGroup::make([
                Actions\Action::make('download_pdf')
                    ->label('Télécharger le PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn (): string => route('quote.pdf', $this->record))
                    ->openUrlInNewTab()
                    ->visible(fn (): bool => $this->hasQuoteItems()),
                Actions\Action::make('download_excel')
                    ->label('Télécharger l\'Excel')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->url(fn (): string => route('quote.excel', $this->record))
                    ->openUrlInNewTab()
                    ->visible(fn (): bool => $this->hasQuoteItems()),
                Actions\Action::make('mark_contacted')
                    ->label('Marquer contacté')
                    ->icon('heroicon-o-phone')
                    ->color('warning')
                    ->visible(fn (): bool => $this->record->status === 'new')
                    ->action(function (): void {
                        $this->record->markAsContacted();
                        $this->refreshRecord();
                        Notification::make()->success()->title('Client marqué comme contacté')->send();
                    }),
                Actions\Action::make('mark_rejected')
                    ->label('Marquer refusé')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (): bool => in_array($this->record->status, ['quoted', 'contacted'], true))
                    ->requiresConfirmation()
                    ->action(function (): void {
                        $this->record->markAsRejected();
                        $this->refreshRecord();
                        Notification::make()->warning()->title('Devis refusé')->send();
                    }),
                Actions\DeleteAction::make()->label('Supprimer'),
            ])
                ->label('Plus d\'actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->button()
                ->color('gray'),
        ];
    }
}
