<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class SiteSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Paramètres';

    protected static ?string $navigationLabel = 'Paramètres du site';

    protected static ?string $title = 'Paramètres du site';

    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        // === ENTREPRISE ===
                        Forms\Components\Tabs\Tab::make('Entreprise')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->label('Nom de l\'entreprise')
                                    ->default('Promo Alu Plus'),
                                Forms\Components\FileUpload::make('company_logo')
                                    ->label('Logo')
                                    ->image()
                                    ->directory('settings'),
                            ]),

                        // === PAGE D'ACCUEIL - HERO ===
                        Forms\Components\Tabs\Tab::make('Accueil - Hero')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Section::make('Badge qualité')
                                    ->description('Le petit texte au-dessus du titre')
                                    ->schema([
                                        Forms\Components\TextInput::make('hero_badge_fr')
                                            ->label('Français')
                                            ->placeholder('Qualité Premium Garantie'),
                                        Forms\Components\TextInput::make('hero_badge_en')
                                            ->label('Anglais'),
                                        Forms\Components\TextInput::make('hero_badge_ar')
                                            ->label('Arabe'),
                                    ])->columns(3),
                                Forms\Components\Section::make('Titre principal')
                                    ->schema([
                                        Forms\Components\TextInput::make('hero_title_fr')
                                            ->label('Français')
                                            ->placeholder('Menuiserie Aluminium'),
                                        Forms\Components\TextInput::make('hero_title_en')
                                            ->label('Anglais'),
                                        Forms\Components\TextInput::make('hero_title_ar')
                                            ->label('Arabe'),
                                    ])->columns(3),
                                Forms\Components\Section::make('Sous-titre')
                                    ->schema([
                                        Forms\Components\TextInput::make('hero_subtitle_fr')
                                            ->label('Français')
                                            ->placeholder('Sur Mesure'),
                                        Forms\Components\TextInput::make('hero_subtitle_en')
                                            ->label('Anglais'),
                                        Forms\Components\TextInput::make('hero_subtitle_ar')
                                            ->label('Arabe'),
                                    ])->columns(3),
                                Forms\Components\Section::make('Description')
                                    ->schema([
                                        Forms\Components\Textarea::make('hero_description_fr')
                                            ->label('Français')
                                            ->rows(2),
                                        Forms\Components\Textarea::make('hero_description_en')
                                            ->label('Anglais')
                                            ->rows(2),
                                        Forms\Components\Textarea::make('hero_description_ar')
                                            ->label('Arabe')
                                            ->rows(2),
                                    ])->columns(3),
                            ]),

                        // === STATISTIQUES ===
                        Forms\Components\Tabs\Tab::make('Statistiques')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\TextInput::make('stats_projects')
                                    ->label('Nombre de projets')
                                    ->numeric()
                                    ->default(500)
                                    ->suffix('+'),
                                Forms\Components\TextInput::make('stats_years')
                                    ->label('Années d\'expérience')
                                    ->numeric()
                                    ->default(15)
                                    ->suffix('+'),
                                Forms\Components\TextInput::make('stats_satisfaction')
                                    ->label('Taux de satisfaction')
                                    ->numeric()
                                    ->default(98)
                                    ->suffix('%'),
                                Forms\Components\TextInput::make('stats_team')
                                    ->label('Membres de l\'équipe')
                                    ->numeric()
                                    ->default(12),
                            ]),

                        // === SECTION CTA ===
                        Forms\Components\Tabs\Tab::make('Section CTA')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Forms\Components\Section::make('Titre CTA')
                                    ->schema([
                                        Forms\Components\TextInput::make('cta_title_fr')
                                            ->label('Français')
                                            ->placeholder('Prêt à Démarrer Votre Projet?'),
                                        Forms\Components\TextInput::make('cta_title_en')
                                            ->label('Anglais'),
                                        Forms\Components\TextInput::make('cta_title_ar')
                                            ->label('Arabe'),
                                    ])->columns(3),
                                Forms\Components\Section::make('Description CTA')
                                    ->schema([
                                        Forms\Components\Textarea::make('cta_description_fr')
                                            ->label('Français')
                                            ->rows(2),
                                        Forms\Components\Textarea::make('cta_description_en')
                                            ->label('Anglais')
                                            ->rows(2),
                                        Forms\Components\Textarea::make('cta_description_ar')
                                            ->label('Arabe')
                                            ->rows(2),
                                    ])->columns(3),
                            ]),

                        // === PAGE À PROPOS ===
                        Forms\Components\Tabs\Tab::make('À propos')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make('Notre histoire')
                                    ->schema([
                                        Forms\Components\Textarea::make('about_story_fr')
                                            ->label('Français')
                                            ->rows(4),
                                        Forms\Components\Textarea::make('about_story_en')
                                            ->label('Anglais')
                                            ->rows(4),
                                        Forms\Components\Textarea::make('about_story_ar')
                                            ->label('Arabe')
                                            ->rows(4),
                                    ])->columns(3),
                                Forms\Components\Section::make('Notre mission')
                                    ->schema([
                                        Forms\Components\Textarea::make('about_mission_fr')
                                            ->label('Français')
                                            ->rows(3),
                                        Forms\Components\Textarea::make('about_mission_en')
                                            ->label('Anglais')
                                            ->rows(3),
                                        Forms\Components\Textarea::make('about_mission_ar')
                                            ->label('Arabe')
                                            ->rows(3),
                                    ])->columns(3),
                                Forms\Components\Section::make('Nos valeurs')
                                    ->schema([
                                        Forms\Components\Textarea::make('about_values_fr')
                                            ->label('Français')
                                            ->rows(3),
                                        Forms\Components\Textarea::make('about_values_en')
                                            ->label('Anglais')
                                            ->rows(3),
                                        Forms\Components\Textarea::make('about_values_ar')
                                            ->label('Arabe')
                                            ->rows(3),
                                    ])->columns(3),
                            ]),

                        // === CONTACT ===
                        Forms\Components\Tabs\Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('contact_phone')
                                    ->label('Téléphone principal')
                                    ->tel()
                                    ->default('+216 12 345 678'),
                                Forms\Components\TextInput::make('contact_phone_2')
                                    ->label('Téléphone secondaire')
                                    ->tel(),
                                Forms\Components\TextInput::make('contact_whatsapp')
                                    ->label('WhatsApp')
                                    ->tel()
                                    ->helperText('Numéro pour le bouton WhatsApp'),
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('Email')
                                    ->email()
                                    ->default('contact@promoaluplus.tn'),
                                Forms\Components\Textarea::make('contact_address')
                                    ->label('Adresse')
                                    ->rows(2),
                                Forms\Components\TextInput::make('contact_map_url')
                                    ->label('Lien Google Maps')
                                    ->url()
                                    ->helperText('URL d\'intégration Google Maps'),
                            ]),

                        // === HORAIRES ===
                        Forms\Components\Tabs\Tab::make('Horaires')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Forms\Components\TextInput::make('hours_weekdays')
                                    ->label('Lundi - Vendredi')
                                    ->default('8h00 - 18h00'),
                                Forms\Components\TextInput::make('hours_saturday')
                                    ->label('Samedi')
                                    ->default('9h00 - 13h00'),
                                Forms\Components\TextInput::make('hours_sunday')
                                    ->label('Dimanche')
                                    ->default('Fermé'),
                            ]),

                        // === RÉSEAUX SOCIAUX ===
                        Forms\Components\Tabs\Tab::make('Réseaux sociaux')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\TextInput::make('social_facebook')
                                    ->label('Facebook')
                                    ->url()
                                    ->placeholder('https://facebook.com/...'),
                                Forms\Components\TextInput::make('social_instagram')
                                    ->label('Instagram')
                                    ->url()
                                    ->placeholder('https://instagram.com/...'),
                                Forms\Components\TextInput::make('social_linkedin')
                                    ->label('LinkedIn')
                                    ->url()
                                    ->placeholder('https://linkedin.com/...'),
                                Forms\Components\TextInput::make('social_youtube')
                                    ->label('YouTube')
                                    ->url()
                                    ->placeholder('https://youtube.com/...'),
                                Forms\Components\TextInput::make('social_tiktok')
                                    ->label('TikTok')
                                    ->url()
                                    ->placeholder('https://tiktok.com/...'),
                            ]),

                        // === SEO ===
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\Section::make('Titre SEO')
                                    ->description('Titre affiché dans les résultats Google (max 60 caractères)')
                                    ->schema([
                                        Forms\Components\TextInput::make('seo_title_fr')
                                            ->label('Français')
                                            ->maxLength(60),
                                        Forms\Components\TextInput::make('seo_title_en')
                                            ->label('Anglais')
                                            ->maxLength(60),
                                        Forms\Components\TextInput::make('seo_title_ar')
                                            ->label('Arabe')
                                            ->maxLength(60),
                                    ])->columns(3),
                                Forms\Components\Section::make('Description SEO')
                                    ->description('Description affichée dans les résultats Google (max 160 caractères)')
                                    ->schema([
                                        Forms\Components\Textarea::make('seo_description_fr')
                                            ->label('Français')
                                            ->maxLength(160)
                                            ->rows(2),
                                        Forms\Components\Textarea::make('seo_description_en')
                                            ->label('Anglais')
                                            ->maxLength(160)
                                            ->rows(2),
                                        Forms\Components\Textarea::make('seo_description_ar')
                                            ->label('Arabe')
                                            ->maxLength(160)
                                            ->rows(2),
                                    ])->columns(3),
                                Forms\Components\TextInput::make('seo_keywords')
                                    ->label('Mots-clés')
                                    ->helperText('Séparés par des virgules'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer les paramètres')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($value !== null) {
                SiteSetting::set($key, $value);
            }
        }

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();
    }
}
