<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $modelLabel = 'Service';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main Tabs Layout
                Tabs::make('Service')
                    ->tabs([
                        // General Tab
                        Tabs\Tab::make('Général')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('Identification')
                                    ->description('Paramètres de base du service')
                                    ->schema([
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Identifiant unique (slug)')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->alphaDash()
                                            ->maxLength(50)
                                            ->helperText('Utilisé dans les URLs et références internes')
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('color')
                                            ->label('Couleur du thème')
                                            ->options([
                                                'blue' => '🔵 Bleu',
                                                'orange' => '🟠 Orange',
                                                'rose' => '🌸 Rose',
                                                'violet' => '🟣 Violet',
                                                'emerald' => '🟢 Émeraude',
                                                'amber' => '🟡 Ambre',
                                                'yellow' => '💛 Jaune',
                                                'teal' => '🩵 Turquoise',
                                                'indigo' => '💜 Indigo',
                                            ])
                                            ->default('blue')
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('icon')
                                            ->label('Nom d\'icône Lucide')
                                            ->placeholder('home, door-open, building')
                                            ->helperText('Nom de l\'icône Lucide (lucide.dev)')
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Ordre d\'affichage')
                                            ->numeric()
                                            ->default(0)
                                            ->columnSpan(1),
                                    ])->columns(4),

                                Forms\Components\Section::make('Icône SVG personnalisée')
                                    ->description('SVG complet pour les icônes complexes (optionnel)')
                                    ->collapsed()
                                    ->schema([
                                        Forms\Components\Textarea::make('svg_icon')
                                            ->label('Code SVG')
                                            ->placeholder('<svg xmlns="http://www.w3.org/2000/svg" ...></svg>')
                                            ->rows(4)
                                            ->helperText('Collez le code SVG complet ici pour des icônes personnalisées'),
                                    ]),

                                Forms\Components\Section::make('Statut')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Service actif')
                                            ->helperText('Désactivez pour masquer ce service du site')
                                            ->default(true)
                                            ->inline(false),
                                    ]),
                            ]),

                        // Content Tab - Multilingual
                        Tabs\Tab::make('Contenu')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('Titre du service')
                                    ->description('Le nom du service dans chaque langue')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('title.fr')
                                                    ->label('🇫🇷 Français')
                                                    ->required()
                                                    ->maxLength(100),
                                                Forms\Components\TextInput::make('title.en')
                                                    ->label('🇬🇧 English')
                                                    ->maxLength(100),
                                                Forms\Components\TextInput::make('title.ar')
                                                    ->label('🇸🇦 العربية')
                                                    ->maxLength(100)
                                                    ->extraInputAttributes(['dir' => 'rtl']),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Description courte')
                                    ->description('Résumé affiché dans les aperçus')
                                    ->schema([
                                        Forms\Components\Tabs::make('short_description_tabs')
                                            ->tabs([
                                                Forms\Components\Tabs\Tab::make('🇫🇷 Français')
                                                    ->schema([
                                                        Forms\Components\Textarea::make('short_description.fr')
                                                            ->label('')
                                                            ->rows(3)
                                                            ->maxLength(300),
                                                    ]),
                                                Forms\Components\Tabs\Tab::make('🇬🇧 English')
                                                    ->schema([
                                                        Forms\Components\Textarea::make('short_description.en')
                                                            ->label('')
                                                            ->rows(3)
                                                            ->maxLength(300),
                                                    ]),
                                                Forms\Components\Tabs\Tab::make('🇸🇦 العربية')
                                                    ->schema([
                                                        Forms\Components\Textarea::make('short_description.ar')
                                                            ->label('')
                                                            ->rows(3)
                                                            ->maxLength(300)
                                                            ->extraInputAttributes(['dir' => 'rtl']),
                                                    ]),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Description complète')
                                    ->description('Description détaillée du service')
                                    ->schema([
                                        Forms\Components\Tabs::make('description_tabs')
                                            ->tabs([
                                                Forms\Components\Tabs\Tab::make('🇫🇷 Français')
                                                    ->schema([
                                                        Forms\Components\RichEditor::make('description.fr')
                                                            ->label('')
                                                            ->toolbarButtons([
                                                                'bold',
                                                                'italic',
                                                                'underline',
                                                                'bulletList',
                                                                'orderedList',
                                                                'h2',
                                                                'h3',
                                                                'link',
                                                            ])
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Tabs\Tab::make('🇬🇧 English')
                                                    ->schema([
                                                        Forms\Components\RichEditor::make('description.en')
                                                            ->label('')
                                                            ->toolbarButtons([
                                                                'bold',
                                                                'italic',
                                                                'underline',
                                                                'bulletList',
                                                                'orderedList',
                                                                'h2',
                                                                'h3',
                                                                'link',
                                                            ])
                                                            ->columnSpanFull(),
                                                    ]),
                                                Forms\Components\Tabs\Tab::make('🇸🇦 العربية')
                                                    ->schema([
                                                        Forms\Components\RichEditor::make('description.ar')
                                                            ->label('')
                                                            ->toolbarButtons([
                                                                'bold',
                                                                'italic',
                                                                'underline',
                                                                'bulletList',
                                                                'orderedList',
                                                                'h2',
                                                                'h3',
                                                                'link',
                                                            ])
                                                            ->extraInputAttributes(['dir' => 'rtl'])
                                                            ->columnSpanFull(),
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                        // Features Tab
                        Tabs\Tab::make('Caractéristiques')
                            ->icon('heroicon-o-check-badge')
                            ->schema([
                                Forms\Components\Section::make('Points forts du service')
                                    ->description('Les avantages et caractéristiques clés affichés avec des coches')
                                    ->schema([
                                        Forms\Components\Repeater::make('features')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('fr')
                                                            ->label('🇫🇷 Français')
                                                            ->required()
                                                            ->placeholder('Ex: Installation rapide et professionnelle'),
                                                        Forms\Components\TextInput::make('en')
                                                            ->label('🇬🇧 English')
                                                            ->placeholder('Ex: Fast and professional installation'),
                                                        Forms\Components\TextInput::make('ar')
                                                            ->label('🇸🇦 العربية')
                                                            ->placeholder('Ex: تركيب سريع واحترافي')
                                                            ->extraInputAttributes(['dir' => 'rtl']),
                                                    ]),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => $state['fr'] ?? null)
                                            ->reorderable()
                                            ->collapsible()
                                            ->collapsed()
                                            ->addActionLabel('Ajouter une caractéristique')
                                            ->defaultItems(0)
                                            ->grid(1),
                                    ]),
                            ]),

                        // Media Tab
                        Tabs\Tab::make('Médias')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make('Image principale')
                                    ->description('Image affichée sur la carte du service. Glissez un fichier ou utilisez le bouton.')
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Fichier')
                                            ->disk('uploads')
                                            ->directory('services')
                                            ->visibility('public')
                                            ->image()
                                            ->imageEditor()
                                            ->imagePreviewHeight('150')
                                            ->maxSize(5120)
                                            ->helperText('JPG, PNG ou WebP — 5 Mo maximum.'),
                                        Forms\Components\TextInput::make('image_url')
                                            ->label('URL externe (optionnel)')
                                            ->maxLength(2048)
                                            ->rules(['nullable', 'string', 'max:2048'])
                                            ->helperText('Utilisée uniquement si aucun fichier n\'est téléversé ci-dessus.'),
                                    ]),

                                Forms\Components\Section::make('Galerie d\'images')
                                    ->description('Glissez-déposez les vignettes pour changer l\'ordre d\'affichage dans le carrousel.')
                                    ->schema([
                                        Forms\Components\FileUpload::make('gallery')
                                            ->label('')
                                            ->disk('uploads')
                                            ->directory('services')
                                            ->visibility('public')
                                            ->image()
                                            ->imageEditor()
                                            ->multiple()
                                            ->reorderable()
                                            ->appendFiles()
                                            ->panelLayout('grid')
                                            ->imagePreviewHeight('120')
                                            ->maxSize(5120)
                                            ->helperText('La première image est utilisée comme image mise en avant.'),
                                    ]),
                            ]),

                        // Technical Tab
                        Tabs\Tab::make('Technique')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Forms\Components\Section::make('Matériaux utilisés')
                                    ->description('Liste des matériaux pour ce service')
                                    ->schema([
                                        Forms\Components\Repeater::make('materials')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('fr')
                                                            ->label('🇫🇷 Français')
                                                            ->required()
                                                            ->placeholder('Ex: Profilés aluminium'),
                                                        Forms\Components\TextInput::make('en')
                                                            ->label('🇬🇧 English')
                                                            ->placeholder('Ex: Aluminum profiles'),
                                                        Forms\Components\TextInput::make('ar')
                                                            ->label('🇸🇦 العربية')
                                                            ->placeholder('Ex: ملفات الألومنيوم')
                                                            ->extraInputAttributes(['dir' => 'rtl']),
                                                    ]),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => $state['fr'] ?? null)
                                            ->reorderable()
                                            ->collapsible()
                                            ->collapsed()
                                            ->addActionLabel('Ajouter un matériau')
                                            ->defaultItems(0),
                                    ]),

                                Forms\Components\Section::make('Spécifications techniques')
                                    ->description('Caractéristiques techniques (épaisseur, dimensions, etc.)')
                                    ->schema([
                                        Forms\Components\Repeater::make('specs')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\TextInput::make('label')
                                                    ->label('Nom')
                                                    ->required()
                                                    ->placeholder('Ex: Épaisseur')
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('value')
                                                    ->label('Valeur')
                                                    ->required()
                                                    ->placeholder('Ex: 1.2 - 2.0 mm')
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2)
                                            ->itemLabel(fn (array $state): ?string => isset($state['label'], $state['value'])
                                                    ? "{$state['label']}: {$state['value']}"
                                                    : null
                                            )
                                            ->reorderable()
                                            ->collapsible()
                                            ->collapsed()
                                            ->addActionLabel('Ajouter une spécification')
                                            ->defaultItems(0),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->size(50)
                    ->getStateUsing(fn (Service $record): ?string => $record->imageSrc()),
                Tables\Columns\TextColumn::make('title.fr')
                    ->label('Service')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Slug copié!')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('color')
                    ->label('Couleur')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'blue' => 'info',
                        'orange' => 'warning',
                        'rose' => 'danger',
                        'violet' => 'gray',
                        'emerald' => 'success',
                        'amber' => 'warning',
                        'yellow' => 'warning',
                        'teal' => 'info',
                        'indigo' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->emptyStateHeading('Aucun service')
            ->emptyStateDescription('Créez votre premier service pour commencer.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Créer un service'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
