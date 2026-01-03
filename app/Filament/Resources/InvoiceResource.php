<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationGroup = 'Finances';
    
    protected static ?string $modelLabel = 'Facture';
    
    protected static ?string $pluralModelLabel = 'Factures';

    protected static ?int $navigationSort = 10;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'draft')->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informations client')
                            ->schema([
                                Forms\Components\TextInput::make('client_name')
                                    ->label('Nom du client')
                                    ->required(),
                                Forms\Components\TextInput::make('client_email')
                                    ->label('Email')
                                    ->email(),
                                Forms\Components\TextInput::make('client_phone')
                                    ->label('Téléphone')
                                    ->tel(),
                                Forms\Components\Textarea::make('client_address')
                                    ->label('Adresse')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('client_tax_id')
                                    ->label('Matricule fiscal'),
                            ])->columns(2),

                        Forms\Components\Section::make('Lignes de facture')
                            ->description('Articles et prestations facturés')
                            ->schema([
                                Forms\Components\Repeater::make('items')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('description')
                                            ->label('Description')
                                            ->required()
                                            ->columnSpan(3),
                                        Forms\Components\Select::make('unit')
                                            ->label('Unité')
                                            ->options([
                                                'unité' => 'Unité',
                                                'm²' => 'm²',
                                                'ml' => 'ml',
                                                'forfait' => 'Forfait',
                                            ])
                                            ->default('unité'),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Qté')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(0.01)
                                            ->live(onBlur: true),
                                        Forms\Components\TextInput::make('unit_price')
                                            ->label('Prix unitaire')
                                            ->numeric()
                                            ->prefix('TND')
                                            ->live(onBlur: true),
                                        Forms\Components\Placeholder::make('line_total')
                                            ->label('Total')
                                            ->content(function ($get) {
                                                $qty = floatval($get('quantity') ?? 0);
                                                $price = floatval($get('unit_price') ?? 0);
                                                return number_format($qty * $price, 2) . ' TND';
                                            }),
                                    ])
                                    ->columns(7)
                                    ->defaultItems(1)
                                    ->addActionLabel('Ajouter une ligne')
                                    ->reorderable()
                                    ->collapsible(),
                            ]),

                        Forms\Components\Section::make('Notes et conditions')
                            ->schema([
                                Forms\Components\Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(2)
                                    ->placeholder('Notes additionnelles pour le client...'),
                                Forms\Components\Textarea::make('terms')
                                    ->label('Conditions de paiement')
                                    ->rows(2)
                                    ->default('Paiement à réception de facture. Tout retard de paiement entraînera des pénalités.'),
                            ])->columns(1),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Facture')
                            ->schema([
                                Forms\Components\TextInput::make('invoice_number')
                                    ->label('N° Facture')
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(fn () => Invoice::generateInvoiceNumber()),
                                Forms\Components\Select::make('quote_id')
                                    ->label('Devis lié')
                                    ->relationship(
                                        'quote',
                                        'quote_number',
                                        fn ($query) => $query->whereNotNull('quote_number')
                                    )
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->quote_number ?? "#{$record->id} - {$record->name}")
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\DatePicker::make('issue_date')
                                    ->label('Date d\'émission')
                                    ->required()
                                    ->default(now()),
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Date d\'échéance')
                                    ->default(now()->addDays(30)),
                                Forms\Components\Select::make('status')
                                    ->label('Statut')
                                    ->options([
                                        'draft' => '📝 Brouillon',
                                        'sent' => '📤 Envoyée',
                                        'paid' => '✅ Payée',
                                        'overdue' => '⚠️ En retard',
                                        'cancelled' => '❌ Annulée',
                                    ])
                                    ->required()
                                    ->default('draft'),
                            ]),

                        Forms\Components\Section::make('Totaux')
                            ->schema([
                                Forms\Components\TextInput::make('discount')
                                    ->label('Remise')
                                    ->numeric()
                                    ->prefix('TND')
                                    ->default(0)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('tax_rate')
                                    ->label('TVA')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(19)
                                    ->live(onBlur: true),
                                Forms\Components\Placeholder::make('calculated_subtotal')
                                    ->label('Sous-total HT')
                                    ->content(fn ($record) => $record ? number_format($record->subtotal ?? 0, 2) . ' TND' : '0.00 TND'),
                                Forms\Components\Placeholder::make('calculated_tax')
                                    ->label('TVA')
                                    ->content(fn ($record) => $record ? number_format($record->tax_amount ?? 0, 2) . ' TND' : '0.00 TND'),
                                Forms\Components\Placeholder::make('calculated_total')
                                    ->label('Total TTC')
                                    ->content(fn ($record) => $record ? number_format($record->total ?? 0, 2) . ' TND' : '0.00 TND'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('N° Facture')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Invoice $record) => $record->status !== 'paid' && $record->due_date && $record->due_date->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total TTC')
                    ->money('TND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'paid' => 'Payée',
                        'overdue' => 'En retard',
                        'cancelled' => 'Annulée',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'info',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'paid' => 'Payée',
                        'overdue' => 'En retard',
                        'cancelled' => 'Annulée',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('mark_sent')
                        ->label('Marquer envoyée')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->visible(fn (Invoice $record) => $record->status === 'draft')
                        ->action(function (Invoice $record) {
                            $record->update(['status' => 'sent']);
                            Notification::make()->success()->title('Facture envoyée')->send();
                        }),
                    Tables\Actions\Action::make('mark_paid')
                        ->label('Marquer payée')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Invoice $record) => in_array($record->status, ['sent', 'overdue']))
                        ->requiresConfirmation()
                        ->action(function (Invoice $record) {
                            $record->update(['status' => 'paid']);
                            Notification::make()->success()->title('Facture payée!')->send();
                        }),
                    Tables\Actions\Action::make('download_pdf')
                        ->label('Télécharger PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->url(fn (Invoice $record) => route('invoice.pdf', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn (Invoice $record) => $record->status === 'draft'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
