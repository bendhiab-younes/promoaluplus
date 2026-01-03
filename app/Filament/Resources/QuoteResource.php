<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Quote;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Demandes';
    
    protected static ?string $modelLabel = 'Demande de devis';
    
    protected static ?string $pluralModelLabel = 'Demandes de devis';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informations client')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Téléphone')
                                    ->tel()
                                    ->required(),
                                Forms\Components\TextInput::make('country')
                                    ->label('Pays'),
                                Forms\Components\TextInput::make('city')
                                    ->label('Ville'),
                            ])->columns(2),
                            
                        Forms\Components\Section::make('Détails du projet')
                            ->schema([
                                Forms\Components\Select::make('project_type')
                                    ->label('Type de projet')
                                    ->options([
                                        'windows' => 'Fenêtres',
                                        'doors' => 'Portes',
                                        'curtains' => 'Rideaux métalliques',
                                        'railings' => 'Garde-corps',
                                        'pergola' => 'Pergola',
                                        'kitchen' => 'Cuisine aluminium',
                                        'shelter' => 'Abri',
                                        'shutters' => 'Volets électriques',
                                        'other' => 'Autre',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('budget_range')
                                    ->label('Budget client'),
                                Forms\Components\TextInput::make('timeline')
                                    ->label('Délai souhaité'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description du projet')
                                    ->required()
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(3),

                        Forms\Components\Section::make('Lignes du devis')
                            ->description('Ajoutez les articles et prestations')
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
                                    ->defaultItems(0)
                                    ->addActionLabel('Ajouter une ligne')
                                    ->reorderable()
                                    ->collapsible(),
                            ])
                            ->collapsed(fn ($record) => $record && $record->items->isEmpty()),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Statut')
                            ->schema([
                                Forms\Components\TextInput::make('quote_number')
                                    ->label('N° Devis')
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\Select::make('status')
                                    ->label('Statut')
                                    ->options([
                                        'new' => '🆕 Nouveau',
                                        'contacted' => '📞 Contacté',
                                        'quoted' => '📋 Devis envoyé',
                                        'accepted' => '✅ Accepté',
                                        'rejected' => '❌ Refusé',
                                        'completed' => '🎉 Terminé',
                                    ])
                                    ->required()
                                    ->default('new'),
                                Forms\Components\DatePicker::make('valid_until')
                                    ->label('Validité jusqu\'au'),
                            ]),

                        Forms\Components\Section::make('Totaux')
                            ->schema([
                                Forms\Components\TextInput::make('discount')
                                    ->label('Remise')
                                    ->numeric()
                                    ->prefix('TND')
                                    ->default(0),
                                Forms\Components\TextInput::make('tax_rate')
                                    ->label('TVA')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(19),
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

                        Forms\Components\Section::make('Notes')
                            ->schema([
                                Forms\Components\Textarea::make('admin_notes')
                                    ->label('Notes internes')
                                    ->rows(3)
                                    ->placeholder('Notes visibles uniquement par l\'admin...'),
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
                Tables\Columns\TextColumn::make('quote_number')
                    ->label('N° Devis')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('project_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'windows' => 'Fenêtres',
                        'doors' => 'Portes',
                        'curtains' => 'Rideaux',
                        'railings' => 'Garde-corps',
                        'pergola' => 'Pergola',
                        'kitchen' => 'Cuisine',
                        'shelter' => 'Abri',
                        'shutters' => 'Volets',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'windows' => 'info',
                        'doors' => 'warning',
                        'kitchen' => 'success',
                        'pergola' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('TND')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Nouveau',
                        'contacted' => 'Contacté',
                        'quoted' => 'Devis envoyé',
                        'accepted' => 'Accepté',
                        'rejected' => 'Refusé',
                        'completed' => 'Terminé',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'danger',
                        'contacted' => 'warning',
                        'quoted' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'gray',
                        'completed' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'new' => 'Nouveau',
                        'contacted' => 'Contacté',
                        'quoted' => 'Devis envoyé',
                        'accepted' => 'Accepté',
                        'rejected' => 'Refusé',
                        'completed' => 'Terminé',
                    ]),
                Tables\Filters\SelectFilter::make('project_type')
                    ->label('Type de projet')
                    ->options([
                        'windows' => 'Fenêtres',
                        'doors' => 'Portes',
                        'curtains' => 'Rideaux',
                        'railings' => 'Garde-corps',
                        'pergola' => 'Pergola',
                        'kitchen' => 'Cuisine',
                        'shelter' => 'Abri',
                        'shutters' => 'Volets',
                        'other' => 'Autre',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('mark_contacted')
                        ->label('Marquer contacté')
                        ->icon('heroicon-o-phone')
                        ->color('warning')
                        ->visible(fn (Quote $record) => $record->status === 'new')
                        ->action(fn (Quote $record) => $record->markAsContacted())
                        ->after(fn () => Notification::make()->success()->title('Statut mis à jour')->send()),
                    Tables\Actions\Action::make('generate_quote')
                        ->label('Générer devis')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->visible(fn (Quote $record) => in_array($record->status, ['new', 'contacted']))
                        ->url(fn (Quote $record) => static::getUrl('edit', ['record' => $record])),
                    Tables\Actions\Action::make('send_quote')
                        ->label('Envoyer devis')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('primary')
                        ->visible(fn (Quote $record) => $record->status === 'contacted' && $record->items->count() > 0)
                        ->requiresConfirmation()
                        ->modalHeading('Envoyer le devis')
                        ->modalDescription('Le devis sera marqué comme envoyé. Voulez-vous continuer?')
                        ->action(function (Quote $record) {
                            $record->markAsQuoted();
                            Notification::make()->success()->title('Devis envoyé')->send();
                        }),
                    Tables\Actions\Action::make('mark_accepted')
                        ->label('Marquer accepté')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Quote $record) => $record->status === 'quoted')
                        ->requiresConfirmation()
                        ->action(function (Quote $record) {
                            $record->markAsAccepted();
                            Notification::make()->success()->title('Devis accepté!')->send();
                        }),
                    Tables\Actions\Action::make('mark_rejected')
                        ->label('Marquer refusé')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Quote $record) => $record->status === 'quoted')
                        ->requiresConfirmation()
                        ->action(function (Quote $record) {
                            $record->markAsRejected();
                            Notification::make()->warning()->title('Devis refusé')->send();
                        }),
                    Tables\Actions\Action::make('download_pdf')
                        ->label('Télécharger PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn (Quote $record) => route('quote.pdf', $record))
                        ->openUrlInNewTab()
                        ->visible(fn (Quote $record) => $record->items->count() > 0),
                    Tables\Actions\Action::make('create_invoice')
                        ->label('Créer facture')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->visible(fn (Quote $record) => $record->status === 'accepted' && !$record->invoice)
                        ->requiresConfirmation()
                        ->modalHeading('Créer une facture')
                        ->modalDescription('Une facture sera créée à partir de ce devis.')
                        ->action(function (Quote $record) {
                            $invoice = $record->createInvoice();
                            $record->markAsCompleted();
                            Notification::make()
                                ->success()
                                ->title('Facture créée')
                                ->body("Facture {$invoice->invoice_number} créée avec succès")
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
