<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
use App\Support\CanonicalServiceCatalog;
use App\Support\DevisPricing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Demandes';

    protected static ?string $modelLabel = 'Devis';

    protected static ?string $pluralModelLabel = 'Devis';

    protected static ?int $navigationSort = 1;

    /**
     * The devis pipeline, in order. Every status label, colour and icon in the
     * admin comes from here so the table, the form and the view page cannot
     * describe the same devis differently.
     *
     * @var array<string, array{label: string, color: string, icon: string}>
     */
    public const STATUSES = [
        'new' => ['label' => 'Nouvelle demande', 'color' => 'danger', 'icon' => 'heroicon-m-inbox-arrow-down'],
        'contacted' => ['label' => 'Client contacté', 'color' => 'warning', 'icon' => 'heroicon-m-phone'],
        'quoted' => ['label' => 'Devis envoyé', 'color' => 'info', 'icon' => 'heroicon-m-paper-airplane'],
        'accepted' => ['label' => 'Accepté', 'color' => 'success', 'icon' => 'heroicon-m-check-circle'],
        'rejected' => ['label' => 'Refusé', 'color' => 'gray', 'icon' => 'heroicon-m-x-circle'],
        'completed' => ['label' => 'Facturé', 'color' => 'success', 'icon' => 'heroicon-m-banknotes'],
    ];

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return array_map(fn (array $status): string => $status['label'], static::STATUSES);
    }

    public static function statusLabel(?string $status): string
    {
        return static::STATUSES[$status]['label'] ?? (string) $status;
    }

    public static function statusColor(?string $status): string
    {
        return static::STATUSES[$status]['color'] ?? 'gray';
    }

    public static function statusIcon(?string $status): ?string
    {
        return static::STATUSES[$status]['icon'] ?? null;
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

    /**
     * Quantities are typed as "2", not "2.00" — trim the decimals the cast adds
     * so the recap lines read the way the admin wrote them.
     */
    protected static function formatQuantity(mixed $quantity): string
    {
        return rtrim(rtrim(number_format((float) $quantity, 2, '.', ''), '0'), '.') ?: '0';
    }

    /**
     * The one-line recap on a collapsed devis line: what it is, how big, how
     * many, and what it costs — so a folded devis stays readable.
     *
     * @param  array<string, mixed>  $state
     */
    protected static function lineItemLabel(array $state): ?string
    {
        $description = trim((string) ($state['description'] ?? ''));

        if ($description === '') {
            return null;
        }

        $parts = [];
        $height = DevisPricing::formatDimension($state['height'] ?? null);
        $width = DevisPricing::formatDimension($state['width'] ?? null);

        if ($height !== '' && $width !== '') {
            $parts[] = $height.' × '.$width.' m';
        }

        $parts[] = '× '.static::formatQuantity($state['quantity'] ?? 0);
        $parts[] = DevisPricing::format(DevisPricing::lineTotal(
            $state['unit_price'] ?? 0,
            $state['shutter_price'] ?? 0,
            $state['quantity'] ?? 0,
        )).' dt';

        return $description.'   —   '.implode('  •  ', $parts);
    }

    /**
     * Re-price every line that references a rate. Needed because editing a rate
     * does not reach back into the lines already entered with it.
     */
    protected static function recalculateAllLines(Forms\Get $get, Forms\Set $set): void
    {
        $rates = $get('rates');
        $items = $get('items') ?? [];
        $touched = 0;

        foreach ($items as $key => $item) {
            $unitPrice = DevisPricing::unitPrice($rates, $item['rate_label'] ?? null, $item['height'] ?? null, $item['width'] ?? null);

            if ($unitPrice !== null) {
                $items[$key]['unit_price'] = $unitPrice;
                $touched++;
            }

            if (filled($item['shutter_rate_label'] ?? null)) {
                $items[$key]['shutter_price'] = DevisPricing::unitPrice(
                    $rates,
                    $item['shutter_rate_label'],
                    $item['height'] ?? null,
                    $item['width'] ?? null,
                ) ?? 0;
            }
        }

        $set('items', $items);

        Notification::make()
            ->success()
            ->title($touched > 0 ? "{$touched} ligne(s) recalculée(s)" : 'Aucune ligne à recalculer')
            ->body($touched > 0 ? 'Pensez à enregistrer le devis.' : 'Aucune ligne n\'utilise un tarif du tableau.')
            ->send();
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
                        Forms\Components\Section::make('1. Client')
                            ->description('À qui s\'adresse le devis. Le nom et l\'adresse sont imprimés en tête du document.')
                            ->icon('heroicon-o-user')
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
                                    ->placeholder('Adresse email du client (facultatif)'),
                                Forms\Components\TextInput::make('client_address')
                                    ->label('Adresse')
                                    ->placeholder('Adresse imprimée sous le nom du client')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('city')
                                    ->label('Ville'),
                                Forms\Components\TextInput::make('country')
                                    ->label('Pays'),
                            ])->columns(2),

                        Forms\Components\Section::make('2. Projet')
                            ->description('Le contexte de la demande — rien de tout ceci n\'apparaît sur le devis imprimé.')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Forms\Components\CheckboxList::make('project_types')
                                    ->label('Type(s) de projet')
                                    ->helperText('Le client peut demander un devis pour plusieurs types à la fois — cochez tout ce qui s\'applique.')
                                    ->options($projectTypeOptions)
                                    ->columns(3)
                                    ->bulkToggleable()
                                    ->rules(['required', 'array', 'min:1'])
                                    ->markAsRequired()
                                    ->default([CanonicalServiceCatalog::OTHER_SLUG])
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('timeline')
                                    ->label('Délai souhaité')
                                    ->placeholder('Délai annoncé par le client'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description du projet')
                                    ->rows(3)
                                    ->placeholder('Ce que le client demande, dans ses mots.')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Section::make('3. Tarifs au m²')
                            ->description('Le barème de ce devis. Chaque ligne de la section 4 s\'y réfère pour calculer son prix, et ce tableau est réimprimé sous le devis.')
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                Forms\Components\Repeater::make('rates')
                                    ->hiddenLabel()
                                    ->schema([
                                        Forms\Components\TextInput::make('label')
                                            ->label('Nom du tarif')
                                            ->placeholder('Matériau ou prestation facturé au m²')
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
                                            ->placeholder('Intitulé imprimé pour ce supplément')
                                            ->columnSpan(3),
                                    ])
                                    ->columns(10)
                                    ->default(DevisPricing::DEFAULT_RATES)
                                    ->addActionLabel('Ajouter un tarif')
                                    ->itemLabel(fn (array $state): ?string => filled($state['label'] ?? null)
                                        ? $state['label'].'  —  '.DevisPricing::format($state['price'] ?? 0).' dt/m²'
                                        : null)
                                    ->live(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('4. Lignes du devis')
                            ->key('devis_lines')
                            ->description('Saisissez les dimensions et choisissez un tarif — le prix se calcule tout seul. Utilisez ⧉ pour dupliquer une menuiserie identique.')
                            ->icon('heroicon-o-table-cells')
                            ->headerActions([
                                Forms\Components\Actions\Action::make('recalculate_all_lines')
                                    ->label('Recalculer les prix')
                                    ->icon('heroicon-m-arrow-path')
                                    ->color('gray')
                                    ->requiresConfirmation()
                                    ->modalHeading('Recalculer toutes les lignes')
                                    ->modalDescription('Les prix seront recalculés à partir des tarifs et des dimensions. Les prix saisis à la main (lignes sans tarif) ne sont pas touchés.')
                                    ->modalSubmitActionLabel('Recalculer')
                                    ->action(fn (Forms\Get $get, Forms\Set $set) => static::recalculateAllLines($get, $set)),
                            ])
                            ->schema([
                                Forms\Components\Repeater::make('items')
                                    ->hiddenLabel()
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('description')
                                            ->label('Désignation')
                                            ->placeholder('Désignation telle qu\'elle apparaîtra sur le devis')
                                            ->required()
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('height')
                                            ->label('Hauteur')
                                            ->numeric()
                                            ->step(0.01)
                                            ->suffix('m')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated($recalculate)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('width')
                                            ->label('Largeur')
                                            ->numeric()
                                            ->step(0.01)
                                            ->suffix('m')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated($recalculate)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Quantité')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(0.01)
                                            ->required()
                                            ->live(onBlur: true)
                                            ->columnSpan(2),
                                        Forms\Components\Select::make('rate_label')
                                            ->label('Tarif menuiserie')
                                            ->options($rateOptions)
                                            ->placeholder('Prix libre (saisi à la main)')
                                            ->live()
                                            ->afterStateUpdated($recalculate)
                                            ->columnSpan(6),

                                        Forms\Components\TextInput::make('unit_price')
                                            ->label('Prix menuiserie / unité')
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('dt')
                                            ->live(onBlur: true)
                                            ->hintAction(
                                                Forms\Components\Actions\Action::make('recalculate_unit_price')
                                                    ->label('Recalculer')
                                                    ->icon('heroicon-m-arrow-path')
                                                    ->tooltip('Recalculer depuis le tarif et les dimensions')
                                                    ->action($recalculate)
                                            )
                                            ->columnSpan(4),
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
                                            ->columnSpan(4),
                                        Forms\Components\TextInput::make('shutter_price')
                                            ->label('Prix volet / unité')
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('dt')
                                            ->live(onBlur: true)
                                            ->columnSpan(4),

                                        Forms\Components\Placeholder::make('line_total')
                                            ->label('Total de la ligne')
                                            ->content(fn (Forms\Get $get): HtmlString => static::lineTotalDisplay($get))
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(12)
                                    ->defaultItems(0)
                                    ->addActionLabel('Ajouter une ligne')
                                    ->reorderable()
                                    ->orderColumn('order')
                                    ->cloneable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => static::lineItemLabel($state)),

                                Forms\Components\Placeholder::make('lines_subtotal')
                                    ->label('Total des lignes')
                                    ->content(fn (Forms\Get $get): HtmlString => new HtmlString(
                                        '<span class="text-xl font-bold">'.e(DevisPricing::format(static::totalsFromState($get)['subtotal'])).' dt</span>'
                                    )),
                            ]),

                        Forms\Components\Section::make('5. Mentions imprimées')
                            ->description('Le bloc « Information sur produit » repris en bas du devis — une puce par ligne.')
                            ->icon('heroicon-o-information-circle')
                            ->collapsed()
                            ->schema([
                                Forms\Components\Textarea::make('product_notes')
                                    ->hiddenLabel()
                                    ->rows(5)
                                    ->default(self::defaultProductNotes())
                                    ->placeholder('Une mention par ligne — chacune devient une puce sous le devis.'),
                            ]),

                        Forms\Components\Section::make('Notes internes')
                            ->description('Visible uniquement dans l\'administration — jamais imprimé.')
                            ->icon('heroicon-o-lock-closed')
                            ->collapsed(fn (?Quote $record): bool => blank($record?->admin_notes))
                            ->schema([
                                Forms\Components\Textarea::make('admin_notes')
                                    ->hiddenLabel()
                                    ->rows(3)
                                    ->placeholder('Remarques de suivi, jamais visibles par le client.'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->extraAttributes(['class' => 'top-6 lg:sticky'])
                    ->schema([
                        Forms\Components\Section::make('Récapitulatif')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Placeholder::make('calculated_subtotal')
                                    ->label('Total des lignes')
                                    ->content(fn (Forms\Get $get): string => DevisPricing::format(static::totalsFromState($get)['subtotal']).' dt'),
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
                                Forms\Components\Placeholder::make('calculated_tax')
                                    ->label('Montant TVA')
                                    ->content(fn (Forms\Get $get): string => DevisPricing::format(static::totalsFromState($get)['tax']).' dt')
                                    ->visible(fn (Forms\Get $get): bool => (bool) $get('show_tax')),
                                Forms\Components\Placeholder::make('calculated_total')
                                    ->label('Net à payer')
                                    ->content(fn (Forms\Get $get): HtmlString => new HtmlString(
                                        '<span class="text-2xl font-bold text-primary-600 dark:text-primary-400">'
                                        .e(DevisPricing::format(static::totalsFromState($get)['total'])).' dt</span>'
                                    )),
                            ]),

                        Forms\Components\Section::make('Suivi')
                            ->icon('heroicon-o-flag')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Étape')
                                    ->options(static::statusOptions())
                                    ->selectablePlaceholder(false)
                                    ->required()
                                    ->default('new')
                                    ->live()
                                    ->helperText(fn (Forms\Get $get): string => static::statusHint($get('status'))),
                                Forms\Components\TextInput::make('quote_number')
                                    ->label('N° du devis')
                                    ->disabled()
                                    ->dehydrated()
                                    ->placeholder('Attribué automatiquement à l\'envoi')
                                    ->helperText('Attribué la première fois que le devis est marqué comme envoyé.'),
                                Forms\Components\DatePicker::make('devis_date')
                                    ->label('Date du devis')
                                    ->displayFormat('d/m/Y')
                                    ->default(now())
                                    ->helperText('Date imprimée sur le document.'),
                                Forms\Components\DatePicker::make('valid_until')
                                    ->label('Valable jusqu\'au')
                                    ->displayFormat('d/m/Y')
                                    ->default(now()->addDays(30)),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    /**
     * The line's total, with the arithmetic that produced it spelled out — the
     * admin can check a price without redoing the multiplication.
     */
    protected static function lineTotalDisplay(Forms\Get $get): HtmlString
    {
        $area = DevisPricing::area($get('height'), $get('width'));
        $unitPrice = (float) $get('unit_price') + (float) $get('shutter_price');
        $quantity = (float) ($get('quantity') ?? 0);
        $total = DevisPricing::lineTotal($get('unit_price'), $get('shutter_price'), $quantity);

        $breakdown = [];

        if ($area > 0) {
            $breakdown[] = DevisPricing::formatDimension($get('height')).' × '.DevisPricing::formatDimension($get('width'))
                .' = '.number_format($area, 3, '.', '').' m²';
        }

        $breakdown[] = DevisPricing::format($unitPrice).' dt × '.static::formatQuantity($quantity);

        return new HtmlString(
            '<span class="text-xl font-bold text-primary-600 dark:text-primary-400">'.e(DevisPricing::format($total)).' dt</span>'
            .' <span class="text-sm text-gray-500">'.e(implode('   •   ', $breakdown)).'</span>'
        );
    }

    /**
     * What the admin should do next, given where the devis sits in the pipeline.
     */
    public static function statusHint(?string $status): string
    {
        return match ($status) {
            'new' => 'Demande reçue — appelez le client, puis chiffrez le devis.',
            'contacted' => 'Client joint — ajoutez les lignes puis envoyez le devis.',
            'quoted' => 'Devis envoyé — en attente de la réponse du client.',
            'accepted' => 'Accepté — vous pouvez créer la facture.',
            'rejected' => 'Refusé — conservé pour l\'historique.',
            'completed' => 'Facturé — dossier clos.',
            default => '',
        };
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make('Client')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Infolists\Components\TextEntry::make('full_name')
                                    ->label('Nom')
                                    ->weight(FontWeight::Bold),
                                Infolists\Components\TextEntry::make('phone')
                                    ->label('Téléphone')
                                    ->icon('heroicon-m-phone')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-m-envelope')
                                    ->copyable()
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('client_address')
                                    ->label('Adresse')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('city')
                                    ->label('Ville')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('country')
                                    ->label('Pays')
                                    ->placeholder('—'),
                            ])->columns(3),

                        Infolists\Components\Section::make('Projet')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Infolists\Components\TextEntry::make('project_types')
                                    ->label('Type(s)')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => Quote::projectTypeLabel($state, 'fr')),
                                Infolists\Components\TextEntry::make('timeline')
                                    ->label('Délai souhaité')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('description')
                                    ->label('Description')
                                    ->placeholder('Aucune description')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Infolists\Components\Section::make('Lignes du devis')
                            ->icon('heroicon-o-table-cells')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('items')
                                    ->hiddenLabel()
                                    ->placeholder('Aucune ligne — le devis n\'est pas encore chiffré.')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('description')
                                            ->hiddenLabel()
                                            ->weight(FontWeight::SemiBold)
                                            ->columnSpanFull(),
                                        Infolists\Components\TextEntry::make('dimensions')
                                            ->label('Dimensions')
                                            ->state(fn ($record): string => DevisPricing::formatDimension($record->height) !== ''
                                                ? DevisPricing::formatDimension($record->height).' × '.DevisPricing::formatDimension($record->width).' m'
                                                : '—')
                                            ->columnSpan(2),
                                        Infolists\Components\TextEntry::make('quantity')
                                            ->label('Quantité')
                                            ->formatStateUsing(fn ($state): string => static::formatQuantity($state)),
                                        Infolists\Components\TextEntry::make('unit_price')
                                            ->label('P.U. menuiserie')
                                            ->formatStateUsing(fn ($state): string => DevisPricing::format($state).' dt'),
                                        Infolists\Components\TextEntry::make('shutter_price')
                                            ->label('P.U. volet')
                                            ->formatStateUsing(fn ($state): string => (float) $state > 0 ? DevisPricing::format($state).' dt' : '—'),
                                        Infolists\Components\TextEntry::make('total')
                                            ->label('Total')
                                            ->weight(FontWeight::Bold)
                                            ->formatStateUsing(fn ($state): string => DevisPricing::format($state).' dt'),
                                    ])
                                    ->columns(6),
                            ]),

                        Infolists\Components\Section::make('Tarifs appliqués')
                            ->icon('heroicon-o-calculator')
                            ->collapsed()
                            ->schema([
                                Infolists\Components\TextEntry::make('rate_legend')
                                    ->hiddenLabel()
                                    ->listWithLineBreaks()
                                    ->state(fn (Quote $record): array => collect($record->rateLegend())
                                        ->map(fn (array $line): string => $line['label'].' : '.DevisPricing::format($line['price']).' dt')
                                        ->all()),
                            ]),

                        Infolists\Components\Section::make('Notes internes')
                            ->icon('heroicon-o-lock-closed')
                            ->visible(fn (Quote $record): bool => filled($record->admin_notes))
                            ->schema([
                                Infolists\Components\TextEntry::make('admin_notes')
                                    ->hiddenLabel(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make()
                    ->extraAttributes(['class' => 'top-6 lg:sticky'])
                    ->schema([
                        Infolists\Components\Section::make('Récapitulatif')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Infolists\Components\TextEntry::make('subtotal')
                                    ->label('Total des lignes')
                                    ->formatStateUsing(fn ($state): string => DevisPricing::format($state).' dt'),
                                Infolists\Components\TextEntry::make('discount')
                                    ->label('Remise')
                                    ->formatStateUsing(fn ($state): string => DevisPricing::format($state).' dt'),
                                Infolists\Components\TextEntry::make('tax_amount')
                                    ->label(fn (Quote $record): string => 'TVA '.static::formatQuantity($record->tax_rate).' %')
                                    ->visible(fn (Quote $record): bool => (bool) $record->show_tax)
                                    ->formatStateUsing(fn ($state): string => DevisPricing::format($state).' dt'),
                                Infolists\Components\TextEntry::make('total')
                                    ->label('Net à payer')
                                    ->formatStateUsing(fn ($state): HtmlString => new HtmlString(
                                        '<span class="text-2xl font-bold text-primary-600 dark:text-primary-400">'
                                        .e(DevisPricing::format($state)).' dt</span>'
                                    )),
                            ]),

                        Infolists\Components\Section::make('Suivi')
                            ->icon('heroicon-o-flag')
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Étape')
                                    ->badge()
                                    ->icon(fn (?string $state): ?string => static::statusIcon($state))
                                    ->color(fn (?string $state): string => static::statusColor($state))
                                    ->formatStateUsing(fn (?string $state): string => static::statusLabel($state))
                                    ->helperText(fn (?string $state): string => static::statusHint($state)),
                                Infolists\Components\TextEntry::make('quote_number')
                                    ->label('N° du devis')
                                    ->placeholder('Pas encore envoyé'),
                                Infolists\Components\TextEntry::make('devis_date')
                                    ->label('Date du devis')
                                    ->date('d/m/Y')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('valid_until')
                                    ->label('Valable jusqu\'au')
                                    ->date('d/m/Y')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Demande reçue le')
                                    ->dateTime('d/m/Y à H:i'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
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
                        'project_types',
                        'total',
                        'status',
                        'created_at',
                    ])
                    ->withCount('items')
                    ->withExists('invoice')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Client')
                    ->searchable(['name', 'first_name', 'quote_number'])
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->formatStateUsing(fn (Quote $record): string => $record->full_name)
                    ->description(fn (Quote $record): string => $record->quote_number ?: 'Brouillon'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Numéro copié')
                    ->icon('heroicon-m-phone')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('project_types')
                    ->label('Type(s)')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Quote::projectTypeLabel($state, 'fr'))
                    ->color(fn (string $state): string => match ($state) {
                        'windows' => 'info',
                        'doors' => 'warning',
                        'kitchen' => 'success',
                        'pergola' => 'primary',
                        default => 'gray',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Lignes')
                    ->alignCenter()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'gray' : 'warning')
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? (string) $state : 'à chiffrer'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Net à payer')
                    ->money('TND')
                    ->sortable()
                    ->alignEnd()
                    ->weight(FontWeight::SemiBold)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Étape')
                    ->badge()
                    ->icon(fn (string $state): ?string => static::statusIcon($state))
                    ->formatStateUsing(fn (string $state): string => static::statusLabel($state))
                    ->color(fn (string $state): string => static::statusColor($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reçue le')
                    ->dateTime('d/m/Y')
                    ->description(fn (Quote $record): string => $record->created_at?->diffForHumans() ?? '')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('project_types')
                    ->label('Type de projet')
                    ->options($projectTypeOptions)
                    ->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        $values = $data['values'] ?? [];

                        if (empty($values)) {
                            return $query;
                        }

                        // Match a devis that asked for ANY of the selected types.
                        return $query->where(function (Builder $query) use ($values) {
                            foreach ($values as $value) {
                                $query->orWhereJsonContains('project_types', $value);
                            }
                        });
                    }),
                Tables\Filters\TernaryFilter::make('items_count')
                    ->label('Chiffrage')
                    ->placeholder('Tous')
                    ->trueLabel('Devis chiffrés')
                    ->falseLabel('Restant à chiffrer')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->has('items'),
                        false: fn (Builder $query): Builder => $query->doesntHave('items'),
                        blank: fn (Builder $query): Builder => $query,
                    ),
            ])
            ->actions([
                // One visible "next step" per row, so the pipeline is legible
                // from the list without opening the ⋮ menu.
                Tables\Actions\Action::make('mark_contacted')
                    ->label('Marquer contacté')
                    ->icon('heroicon-m-phone')
                    ->color('warning')
                    ->iconButton()
                    ->tooltip('Marquer comme contacté')
                    ->visible(fn (Quote $record): bool => $record->status === 'new')
                    ->action(function (Quote $record): void {
                        $record->markAsContacted();
                        Notification::make()->success()->title('Client marqué comme contacté')->send();
                    }),
                Tables\Actions\Action::make('build_quote')
                    ->label('Chiffrer')
                    ->icon('heroicon-m-calculator')
                    ->color('primary')
                    ->button()
                    ->visible(fn (Quote $record): bool => in_array($record->status, ['new', 'contacted'], true) && $record->items_count === 0)
                    ->url(fn (Quote $record): string => static::getUrl('edit', ['record' => $record])),
                Tables\Actions\Action::make('send_quote')
                    ->label('Envoyer')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('primary')
                    ->button()
                    ->visible(fn (Quote $record): bool => in_array($record->status, ['new', 'contacted'], true) && $record->items_count > 0)
                    ->requiresConfirmation()
                    ->modalHeading('Marquer le devis comme envoyé')
                    ->modalDescription('Un numéro de devis sera attribué s\'il n\'en a pas encore. Pensez à envoyer le PDF au client.')
                    ->modalSubmitActionLabel('Marquer comme envoyé')
                    ->action(function (Quote $record): void {
                        $record->markAsQuoted();
                        Notification::make()
                            ->success()
                            ->title('Devis envoyé')
                            ->body("Numéro attribué : {$record->quote_number}")
                            ->send();
                    }),
                Tables\Actions\Action::make('mark_accepted')
                    ->label('Accepté')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->button()
                    ->visible(fn (Quote $record): bool => $record->status === 'quoted')
                    ->requiresConfirmation()
                    ->modalHeading('Le client a accepté le devis')
                    ->action(function (Quote $record): void {
                        $record->markAsAccepted();
                        Notification::make()->success()->title('Devis accepté !')->send();
                    }),
                Tables\Actions\Action::make('create_invoice')
                    ->label('Facturer')
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->button()
                    ->visible(fn (Quote $record): bool => $record->status === 'accepted' && ! $record->invoice_exists)
                    ->requiresConfirmation()
                    ->modalHeading('Créer une facture')
                    ->modalDescription('Une facture reprenant les lignes et les totaux de ce devis sera créée.')
                    ->action(function (Quote $record): void {
                        $invoice = $record->createInvoice();
                        $record->markAsCompleted();
                        Notification::make()
                            ->success()
                            ->title('Facture créée')
                            ->body("Facture {$invoice->invoice_number} créée avec succès")
                            ->send();
                    }),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Voir le détail'),
                    Tables\Actions\EditAction::make()->label('Modifier'),
                    Tables\Actions\Action::make('download_pdf')
                        ->label('Télécharger le PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->url(fn (Quote $record): string => route('quote.pdf', $record))
                        ->openUrlInNewTab()
                        ->visible(fn (Quote $record): bool => $record->items_count > 0),
                    Tables\Actions\Action::make('download_excel')
                        ->label('Télécharger l\'Excel')
                        ->icon('heroicon-o-table-cells')
                        ->color('success')
                        ->url(fn (Quote $record): string => route('quote.excel', $record))
                        ->openUrlInNewTab()
                        ->visible(fn (Quote $record): bool => $record->items_count > 0),
                    Tables\Actions\Action::make('mark_rejected')
                        ->label('Marquer refusé')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Quote $record): bool => in_array($record->status, ['quoted', 'contacted'], true))
                        ->requiresConfirmation()
                        ->action(function (Quote $record): void {
                            $record->markAsRejected();
                            Notification::make()->warning()->title('Devis refusé')->send();
                        }),
                    Tables\Actions\DeleteAction::make()->label('Supprimer'),
                ])
                    ->tooltip('Plus d\'actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucun devis')
            ->emptyStateDescription('Les demandes envoyées depuis le site apparaissent ici. Vous pouvez aussi créer un devis à la main.')
            ->emptyStateIcon('heroicon-o-document-text');
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
            'view' => Pages\ViewQuote::route('/{record}'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
