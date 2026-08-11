<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroSlideResource\Pages;
use App\Models\HeroSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $modelLabel = 'Slide d\'accueil';

    protected static ?string $pluralModelLabel = 'Slides d\'accueil';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Slide')->tabs([
                Forms\Components\Tabs\Tab::make('Contenu')
                    ->icon('heroicon-o-language')
                    ->schema([
                        Forms\Components\Section::make('Badge')->schema([
                            Forms\Components\TextInput::make('badge.fr')->label('🇫🇷 Français'),
                            Forms\Components\TextInput::make('badge.en')->label('🇬🇧 English'),
                            Forms\Components\TextInput::make('badge.ar')->label('🇸🇦 العربية')->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3),
                        Forms\Components\Section::make('Titre')->schema([
                            Forms\Components\TextInput::make('title.fr')->label('🇫🇷 Français')->rules(['required', 'string'])->markAsRequired(),
                            Forms\Components\TextInput::make('title.en')->label('🇬🇧 English'),
                            Forms\Components\TextInput::make('title.ar')->label('🇸🇦 العربية')->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3),
                        Forms\Components\Section::make('Accroche (deuxième ligne, colorée)')->schema([
                            Forms\Components\TextInput::make('highlight.fr')->label('🇫🇷 Français'),
                            Forms\Components\TextInput::make('highlight.en')->label('🇬🇧 English'),
                            Forms\Components\TextInput::make('highlight.ar')->label('🇸🇦 العربية')->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3),
                        Forms\Components\Section::make('Description')->schema([
                            Forms\Components\Textarea::make('description.fr')->label('🇫🇷 Français')->rows(2),
                            Forms\Components\Textarea::make('description.en')->label('🇬🇧 English')->rows(2),
                            Forms\Components\Textarea::make('description.ar')->label('🇸🇦 العربية')->rows(2)->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3),
                    ]),

                Forms\Components\Tabs\Tab::make('Image')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image de fond')
                            ->disk('uploads')
                            ->directory('hero')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['16:9', '21:9', null])
                            ->imagePreviewHeight('260')
                            ->maxSize(8192)
                            ->helperText('C\'est l\'image affichée sur le site. Glissez une nouvelle photo ici pour la remplacer.'),
                        // Rare fallback: a slide whose image only lives at an external
                        // URL has nothing a FileUpload can preview or let an admin
                        // replace by dragging. Show it read-only, and point at the
                        // field above as the way to actually change it.
                        Forms\Components\Placeholder::make('current_image')
                            ->label('Image actuelle (lien externe)')
                            ->content(function (?HeroSlide $record): HtmlString {
                                $src = $record?->imageSrc();

                                return new HtmlString(
                                    '<img src="'.e($src).'" alt="" style="max-height:220px;max-width:100%;border-radius:0.5rem;object-fit:cover;">'
                                );
                            })
                            ->helperText('Pour la remplacer, déposez un fichier dans le champ ci-dessus.')
                            ->visible(fn (?HeroSlide $record): bool => $record !== null && blank($record->image) && filled($record->imageSrc())),
                        Forms\Components\TextInput::make('image_url')
                            ->label('URL externe (optionnel)')
                            ->maxLength(2048)
                            ->rules(['nullable', 'string', 'max:2048'])
                            ->helperText('Utilisée uniquement si aucun fichier n\'est déposé ci-dessus.'),
                        Forms\Components\Section::make('Cadrage')
                            ->description('Réglages non destructifs — modifiables à tout moment sans re-téléverser l\'image.')
                            ->schema([
                                Forms\Components\Radio::make('image_fit')
                                    ->label('Mode d\'affichage')
                                    ->options([
                                        'cover' => 'Remplir — l\'image remplit le cadre, les bords sont rognés',
                                        'contain' => 'Entier — toute l\'image est visible sur un fond flouté',
                                    ])
                                    ->default('cover')
                                    ->rules(['required', 'in:cover,contain'])
                                    ->markAsRequired()
                                    ->inline(false),
                                Forms\Components\ToggleButtons::make('image_zoom')
                                    ->label('Zoom')
                                    ->helperText('Comme un zoom appareil photo : rapproche l\'image.')
                                    ->options([
                                        100 => 'Normal',
                                        125 => 'Un peu zoomée',
                                        150 => 'Zoomée',
                                        175 => 'Très zoomée',
                                        200 => 'Maximum',
                                    ])
                                    ->default(100)
                                    ->inline()
                                    ->grouped()
                                    ->rules(['required', 'integer', 'min:100', 'max:200'])
                                    ->markAsRequired()
                                    ->columnSpanFull(),
                                Forms\Components\ToggleButtons::make('focal_x')
                                    ->label('Partie à garder visible — horizontal')
                                    ->options([0 => 'Gauche', 50 => 'Centre', 100 => 'Droite'])
                                    ->default(50)
                                    ->inline()
                                    ->grouped()
                                    ->rules(['required', 'integer', 'min:0', 'max:100'])
                                    ->markAsRequired()
                                    ->helperText('Quand l\'image est zoomée, quelle partie rester visible ?'),
                                Forms\Components\ToggleButtons::make('focal_y')
                                    ->label('Partie à garder visible — vertical')
                                    ->options([0 => 'Haut', 50 => 'Centre', 100 => 'Bas'])
                                    ->default(50)
                                    ->inline()
                                    ->grouped()
                                    ->rules(['required', 'integer', 'min:0', 'max:100'])
                                    ->markAsRequired(),
                            ])->columns(2),
                        Forms\Components\Section::make('Texte alternatif (accessibilité et SEO)')->schema([
                            Forms\Components\TextInput::make('alt_text.fr')->label('🇫🇷 Français'),
                            Forms\Components\TextInput::make('alt_text.en')->label('🇬🇧 English'),
                            Forms\Components\TextInput::make('alt_text.ar')->label('🇸🇦 العربية')->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3),
                    ]),

                Forms\Components\Tabs\Tab::make('Bouton & affichage')
                    ->icon('heroicon-o-cursor-arrow-rays')
                    ->schema([
                        Forms\Components\Select::make('cta_type')
                            ->label('Destination du bouton')
                            ->options([
                                'contact' => 'Page Contact',
                                'services' => 'Page Services',
                                'portfolio' => 'Page Réalisations (bascule vers Services si masquée)',
                                'custom' => 'URL personnalisée',
                                'none' => 'Aucun bouton',
                            ])
                            ->default('contact')
                            ->native(false)
                            ->rules(['required', 'in:'.implode(',', HeroSlide::CTA_TYPES)])
                            ->markAsRequired()
                            ->live(),
                        Forms\Components\TextInput::make('cta_url')
                            ->label('URL personnalisée')
                            ->maxLength(2048)
                            ->visible(fn (Forms\Get $get): bool => $get('cta_type') === 'custom')
                            ->rules(['nullable', 'string', 'max:2048']),
                        Forms\Components\Section::make('Libellé du bouton')->schema([
                            Forms\Components\TextInput::make('cta_label.fr')->label('🇫🇷 Français'),
                            Forms\Components\TextInput::make('cta_label.en')->label('🇬🇧 English'),
                            Forms\Components\TextInput::make('cta_label.ar')->label('🇸🇦 العربية')->extraInputAttributes(['dir' => 'rtl']),
                        ])->columns(3)->visible(fn (Forms\Get $get): bool => $get('cta_type') !== 'none'),
                        Forms\Components\Toggle::make('show_whatsapp')
                            ->label('Afficher aussi le bouton WhatsApp'),
                        Forms\Components\Select::make('accent_color')
                            ->label('Couleur d\'accent')
                            ->options([
                                'orange' => 'Orange', 'blue' => 'Bleu', 'cyan' => 'Cyan', 'emerald' => 'Vert',
                            ])
                            ->default('orange')
                            ->native(false)
                            ->rules(['required', 'in:'.implode(',', HeroSlide::ACCENT_COLORS)])
                            ->markAsRequired(),
                        Forms\Components\TextInput::make('badge_icon')
                            ->label('Icône du badge')
                            ->default('star')
                            ->rules(['required', 'string', 'max:100'])
                            ->markAsRequired()
                            ->helperText('Nom d\'icône Lucide, par ex. star, home, layout-grid, shield.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Slide visible')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0)
                            ->rules(['required', 'integer'])
                            ->markAsRequired(),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Aperçu')
                    ->getStateUsing(fn (HeroSlide $record): ?string => $record->imageSrc()),
                Tables\Columns\TextColumn::make('title.fr')->label('Titre')->searchable(),
                Tables\Columns\TextColumn::make('cta_type')->label('Bouton')->badge(),
                Tables\Columns\IconColumn::make('is_active')->label('Visible')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Ordre')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Visible'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order');
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
            'index' => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit' => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }
}
