<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

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
                        // === PAGES & VISIBILITÉ ===
                        Forms\Components\Tabs\Tab::make('Pages & visibilité')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Forms\Components\Section::make('Page Réalisations')
                                    ->description('Affichez ou masquez la page Réalisations sur le site public. Lorsqu\'elle est masquée, la page renvoie une erreur 404 et tous les liens vers celle-ci disparaissent du menu, du pied de page et des boutons d\'appel à l\'action.')
                                    ->schema([
                                        Forms\Components\Toggle::make('portfolio_enabled')
                                            ->label('Afficher la page Réalisations')
                                            ->helperText('Ajoutez d\'abord vos projets dans Contenu → Projets, puis activez cette option.')
                                            ->default(false),
                                    ]),
                            ]),

                        // === ENTREPRISE ===
                        Forms\Components\Tabs\Tab::make('Entreprise')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->label('Nom de l\'entreprise')
                                    ->default('PromoAlu+'),
                                Forms\Components\FileUpload::make('company_logo')
                                    ->label('Logo')
                                    ->image()
                                    ->directory('settings'),
                                Forms\Components\TextInput::make('company_tax_id')
                                    ->label('Matricule fiscal (MF)')
                                    ->default('1901901B')
                                    ->helperText('Affiché dans le bloc "Prestataire" en tête des devis PDF.'),
                            ]),

                        // Le hero de la page d'accueil n'est plus édité ici : il se gère
                        // dans Contenu → Slides d'accueil (HeroSlideResource), qui alimente
                        // directement le carrousel. Les lignes hero_badge_*, hero_title_*,
                        // hero_subtitle_* et hero_description_* restent dans site_settings
                        // car HeroSlideSeeder les lit encore comme surcharges facultatives
                        // au moment du seed (à défaut, il retombe sur les fichiers lang).

                        // L'onglet « Statistiques » (stats_projects, stats_years,
                        // stats_satisfaction, stats_team) a été retiré : le bloc de
                        // compteurs qu'il alimentait n'existe plus sur la page
                        // d'accueil, donc ces champs s'enregistraient sans jamais
                        // rien changer. Le chiffre des années d'expérience se
                        // modifie désormais directement dans le titre de la 4e
                        // slide (Contenu → Slides d'accueil).

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
                                Forms\Components\Section::make('Photo de la section « Notre histoire »')
                                    ->description('Photo affichée à côté du texte « Notre histoire » sur la page À propos.')
                                    ->schema([
                                        Forms\Components\FileUpload::make('about_story_image')
                                            ->label('Photo')
                                            ->disk('uploads')
                                            ->directory('about')
                                            ->visibility('public')
                                            ->image()
                                            ->imageEditor()
                                            ->imagePreviewHeight('220')
                                            ->maxSize(8192)
                                            ->helperText('Glissez une photo ici pour remplacer celle affichée sur le site.'),
                                        Forms\Components\TextInput::make('about_story_image_url')
                                            ->label('… ou lien vers une image externe')
                                            ->helperText('Utilisé uniquement si aucune photo n\'est envoyée ci-dessus.'),
                                    ])->columns(2),
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
                                Forms\Components\Section::make('Notre vision')
                                    ->schema([
                                        Forms\Components\Textarea::make('about_vision_fr')
                                            ->label('Français')
                                            ->rows(3),
                                        Forms\Components\Textarea::make('about_vision_en')
                                            ->label('Anglais')
                                            ->rows(3),
                                        Forms\Components\Textarea::make('about_vision_ar')
                                            ->label('Arabe')
                                            ->rows(3),
                                    ])->columns(3),
                                Forms\Components\Section::make('Nos valeurs')
                                    ->description('Une valeur par ligne. Format « Titre : description » — le titre s\'affiche en gras sous une icône, la description en dessous.')
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
                                    ->default('+21626192898')
                                    ->helperText('Affiché aussi dans le bloc "Prestataire" des devis PDF.'),
                                Forms\Components\TextInput::make('contact_whatsapp')
                                    ->label('WhatsApp')
                                    ->tel()
                                    ->default('+21626192898')
                                    ->helperText('Numéro pour le bouton WhatsApp'),
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('Email')
                                    ->email()
                                    ->default('promoaluplus@gmail.com')
                                    ->helperText('Affiché aussi dans le bloc "Prestataire" des devis PDF.'),
                                Forms\Components\Textarea::make('contact_address')
                                    ->label('Adresse')
                                    ->rows(2)
                                    ->helperText('Affichée aussi dans le bloc "Prestataire" des devis PDF.'),
                                // Retirés : « Téléphone secondaire » (contact_phone_2)
                                // et « Lien Google Maps » (contact_map_url). Aucun des
                                // deux n'était lu nulle part — il n'y a pas de second
                                // numéro affiché sur le site, ni de carte intégrée sur
                                // la page Contact.
                            ]),

                        // L'onglet « Horaires » (hours_weekdays, hours_saturday,
                        // hours_sunday) a été retiré : le site n'affiche nulle part
                        // un tableau d'horaires. La page Contact montre la mention
                        // messages.working_hours, qui vient des fichiers de langue.

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
            if ($value === null) {
                continue;
            }

            SiteSetting::set($key, is_bool($value) ? ($value ? '1' : '0') : $value);
        }

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();
    }
}
