<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
use App\Support\CanonicalServiceCatalog;
use App\Support\DevisPricing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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

    /**
     * Recompute a line's prices from the devis rate table. Only prices backed
     * by a rate are touched, so a hand-typed one-off price survives an edit to
     * the dimensions next to it.
     */
    protected static function recalculateLine(Forms\Get $get, Forms\Set $set): void
    {
        $rates = $get('../../rates');
        $height = $get('height');
        $width = $get('width');

        $unitPrice = DevisPricing::unitPrice($rates, $get('rate_label'), $height, $width);

        if ($unitPrice !== null) {
            $set('unit_price', $unitPrice);
        }

        if (filled($get('shutter_rate_label'))) {
            $set('shutter_price', DevisPricing::unitPrice($rates, $get('shutter_rate_label'), $height, $width) ?? 0);
        }
    }

    /**
     * Totals straight from the form state, so the sidebar tracks what the
     * admin is typing instead of the last saved figures.
     *
     * @return array{subtotal: float, discount: float, tax: float, total: float}
     */
    protected static function totalsFromState(Forms\Get $get): array
    {
        $subtotal = collect($get('items') ?? [])
            ->sum(fn ($item): float => DevisPricing::lineTotal(
                $item['unit_price'] ?? 0,
                $item['shutter_price'] ?? 0,
                $item['quantity'] ?? 0,
            ));

        $discount = (float) ($get('discount') ?? 0);
        $taxRate = $get('show_tax') ? (float) ($get('tax_rate') ?? 19) : 0.0;
        $tax = ($subtotal - $discount) * ($taxRate / 100);

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'tax' => round($tax, 2),
            'total' => round($subtotal - $discount + $tax, 2),
        ];
    }

    public static function form(Form $form): Form
    {
        $projectTypeOptions = Quote::projectTypeOptions('fr');
        $rateOptions = fn (Forms\Get $get): array => DevisPricing::rateOptions($get('../../rates'));
        $recalculate = fn (Forms\Get $get, Forms\Set $set) => static::recalculateLine($get, $set);

        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informations client')
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('Prénom')
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom de famille')
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Téléphone')
                                    ->tel()
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->placeholder('Facultatif'),
                                Forms\Components\TextInput::make('client_address')
                                    ->label('Adresse')
                                    ->placeholder('Sousse-Khzema')
                                    ->helperText('Imprimée sous le nom du client sur le devis')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('city')
                                    ->label('Ville'),
                                Forms\Components\TextInput::make('country')
                                    ->label('Pays'),
                            ])->columns(2),

                        Forms\Components\Section::make('Détails du projet')
                            ->schema([
                                Forms\Components\Select::make('project_type')
                                    ->label('Type de projet')
                                    ->options($projectTypeOptions)
                                    ->required()
                                    ->default(CanonicalServiceCatalog::OTHER_SLUG),
                                Forms\Components\TextInput::make('budget_range')
                                    ->label('Budget client'),
                                Forms\Components\TextInput::make('timeline')
                                    ->label('Délai souhaité'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description du projet')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(3),

                        Forms\Components\Section::make('Tarifs (prix au m²)')
                            ->description('Les prix des lignes sont calculés à partir de ces tarifs. Ce tableau est imprimé sous le devis.')
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                Forms\Components\Repeater::make('rates')
                                    ->hiddenLabel()
                                    ->schema([
                                        Forms\Components\TextInput::make('label')
                                            ->label('Tarif')
                                            ->placeholder('Aluminium')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->columnSpan(3),
                                        Forms\Components\TextInput::make('price')
                                            ->label('Prix / m²')
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->suffix('dt')
                                            ->live(onBlur: true)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('supplement')
                                            ->label('Supplément fixe')
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('dt')
                                            ->helperText('Facturé une fois par unité (ex. moteur)')
                                            ->live(onBlur: true)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('supplement_label')
                                            ->label('Libellé du supplément')
                                            ->placeholder('Prix Moteur')
                                            ->columnSpan(3),
                                    ])
                                    ->columns(10)
                                    ->default(DevisPricing::DEFAULT_RATES)
                                    ->addActionLabel('Ajouter un tarif')
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                    ->live(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('Lignes du devis')
                            ->description('Saisissez les dimensions et choisissez un tarif — le prix se calcule tout seul.')
                            ->icon('heroicon-o-table-cells')
                            ->schema([
                                Forms\Components\Repeater::make('items')
                                    ->hiddenLabel()
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('description')
                                            ->label('Désignation')
                                            ->placeholder('Fenêtre à la française 2 ventaux')
                                            ->required()
                                            ->columnSpan(4),
                                        Forms\Components\TextInput::make('height')
                                            ->label('H (m)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated($recalculate)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('width')
                                            ->label('L (m)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated($recalculate)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Qté')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(0.01)
                                            ->required()
                                            ->live(onBlur: true)
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('rate_label')
                                            ->label('Tarif')
                                            ->options($rateOptions)
                                            ->placeholder('Prix libre')
                                            ->live()
                                            ->afterStateUpdated($recalculate)
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('unit_price')
                                            ->label('Prix unitaire')
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('dt')
                                            ->live(onBlur: true)
                                            ->helperText('Modifiable — le ↻ rétablit le calcul')
                                            ->suffixAction(
                                                Forms\Components\Actions\Action::make('recalculate_unit_price')
                                                    ->icon('heroicon-m-arrow-path')
                                                    ->tooltip('Recalculer depuis le tarif')
                                                    ->action($recalculate)
                                            )
                                            ->columnSpan(3),
                                        Forms\Components\Select::make('shutter_rate_label')
                                            ->label('Tarif volet')
                                            ->options($rateOptions)
                                            ->placeholder('Sans volet')
                                            ->live()
                                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, $state): void {
                                                $set('shutter_price', blank($state)
                                                    ? 0
                                                    : (DevisPricing::unitPrice($get('../../rates'), $state, $get('height'), $get('width')) ?? 0));
                                            })
                                            ->columnSpan(3),
                                        Forms\Components\TextInput::make('shutter_price')
                                            ->label('Prix volet')
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('dt')
                                            ->live(onBlur: true)
                                            ->columnSpan(3),
                                        Forms\Components\Placeholder::make('line_total')
                                            ->label('Total ligne')
                                            ->content(fn (Forms\Get $get): string => DevisPricing::format(
                                                DevisPricing::lineTotal($get('unit_price'), $get('shutter_price'), $get('quantity'))
                                            ).' dt')
                                            ->columnSpan(3),
                                    ])
                                    ->columns(12)
                                    ->defaultItems(0)
                                    ->addActionLabel('Ajouter une ligne')
                                    ->reorderable()
                                    ->orderColumn('order')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['description'] ?? null),
                            ])
                            ->collapsed(fn ($record) => $record && ! $record->items()->exists()),

                        Forms\Components\Section::make('Information sur produit')
                            ->description('Bloc imprimé en bas du devis — une puce par ligne.')
                            ->icon('heroicon-o-information-circle')
                            ->collapsed()
                            ->schema([
                                Forms\Components\Textarea::make('product_notes')
                                    ->hiddenLabel()
                                    ->rows(5)
                                    ->default(self::defaultProductNotes())
                                    ->placeholder(self::defaultProductNotes()),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Statut')
                            ->schema([
                                Forms\Components\TextInput::make('quote_number')
                                    ->label('N° Devis')
                                    ->disabled()
                                    ->dehydrated()
                                    ->placeholder('Attribué à l\'envoi'),
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
                                Forms\Components\DatePicker::make('devis_date')
                                    ->label('Date du devis')
                                    ->displayFormat('d/m/Y')
                                    ->default(now())
                                    ->helperText('Date imprimée sur le document'),
                                Forms\Components\DatePicker::make('valid_until')
                                    ->label('Validité jusqu\'au')
                                    ->displayFormat('d/m/Y'),
                            ]),

                        Forms\Components\Section::make('Totaux')
                            ->schema([
                                Forms\Components\TextInput::make('discount')
                                    ->label('Remise')
                                    ->numeric()
                                    ->suffix('dt')
                                    ->default(0)
                                    ->live(onBlur: true),
                                Forms\Components\Toggle::make('show_tax')
                                    ->label('Appliquer la TVA')
                                    ->helperText('Désactivé : devis HTVA, comme les devis papier.')
                                    ->default(false)
                                    ->live(),
                                Forms\Components\TextInput::make('tax_rate')
                                    ->label('Taux de TVA')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(19)
                                    ->live(onBlur: true)
                                    ->visible(fn (Forms\Get $get): bool => (bool) $get('show_tax')),
                                Forms\Components\Placeholder::make('calculated_subtotal')
                                    ->label('Total')
                                    ->content(fn (Forms\Get $get): string => DevisPricing::format(static::totalsFromState($get)['subtotal']).' dt'),
                                Forms\Components\Placeholder::make('calculated_discount')
                                    ->label('Remise')
                                    ->content(fn (Forms\Get $get): string => DevisPricing::format(static::totalsFromState($get)['discount']).' dt'),
                                Forms\Components\Placeholder::make('calculated_tax')
                                    ->label('TVA')
                                    ->content(fn (Forms\Get $get): string => DevisPricing::format(static::totalsFromState($get)['tax']).' dt')
                                    ->visible(fn (Forms\Get $get): bool => (bool) $get('show_tax')),
                                Forms\Components\Placeholder::make('calculated_total')
                                    ->label('Net à payer')
                                    ->content(fn (Forms\Get $get): string => DevisPricing::format(static::totalsFromState($get)['total']).' dt'),
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

    /**
     * The product blurb the workshop prints on its joinery devis.
     */
    protected static function defaultProductNotes(): string
    {
        return implode("\n", [
            'Aluminium 1 choix TPR OU ALLUCO OU PALMA avec accessoires made in italy',
            'Double vitrage 1.8mm stop sol marron clair',
            'Lame FLORA 5.5cm blanc',
            '5ans garentie pour les moteurs',
            'Y compris fourniture et pose',
        ]);
    }

    public static function table(Table $table): Table
    {
        $projectTypeOptions = Quote::projectTypeOptions('fr');

        return $table
            ->modifyQueryUsing(
                fn (Builder $query): Builder => $query
                    ->select([
                        'id',
                        'quote_number',
                        'first_name',
                        'name',
                        'phone',
                        'project_type',
                        'total',
                        'status',
                        'created_at',
                    ])
                    ->withCount('items')
                    ->withExists('invoice')
            )
            ->columns([
                Tables\Columns\TextColumn::make('quote_number')
                    ->label('N° Devis')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (Quote $record): string => $record->full_name),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('project_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Quote::projectTypeLabel($state, 'fr'))
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
                    ->options($projectTypeOptions),
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
                        ->visible(fn (Quote $record) => $record->status === 'contacted' && $record->items_count > 0)
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
                        ->label('Devis PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->url(fn (Quote $record) => route('quote.pdf', $record))
                        ->openUrlInNewTab()
                        ->visible(fn (Quote $record) => $record->items_count > 0),
                    Tables\Actions\Action::make('download_excel')
                        ->label('Devis Excel')
                        ->icon('heroicon-o-table-cells')
                        ->color('success')
                        ->url(fn (Quote $record) => route('quote.excel', $record))
                        ->openUrlInNewTab()
                        ->visible(fn (Quote $record) => $record->items_count > 0),
                    Tables\Actions\Action::make('create_invoice')
                        ->label('Créer facture')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->visible(fn (Quote $record) => $record->status === 'accepted' && ! $record->invoice_exists)
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
