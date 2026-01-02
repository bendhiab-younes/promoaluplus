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
                        Forms\Components\Tabs\Tab::make('Entreprise')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->label('Nom de l\'entreprise')
                                    ->default('Promo Alu Plus'),
                                Forms\Components\TextInput::make('company_slogan')
                                    ->label('Slogan')
                                    ->default('Menuiserie Aluminium & Inox de Qualité'),
                                Forms\Components\Textarea::make('company_description')
                                    ->label('Description courte')
                                    ->rows(3),
                                Forms\Components\FileUpload::make('company_logo')
                                    ->label('Logo')
                                    ->image()
                                    ->directory('settings'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\TextInput::make('contact_phone')
                                    ->label('Téléphone')
                                    ->tel()
                                    ->default('+216 12 345 678'),
                                Forms\Components\TextInput::make('contact_phone_2')
                                    ->label('Téléphone secondaire')
                                    ->tel(),
                                Forms\Components\TextInput::make('contact_whatsapp')
                                    ->label('WhatsApp')
                                    ->tel()
                                    ->default('+216 12 345 678'),
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('Email')
                                    ->email()
                                    ->default('contact@promoaluplus.tn'),
                                Forms\Components\Textarea::make('contact_address')
                                    ->label('Adresse')
                                    ->rows(2)
                                    ->default('Tunis, Tunisie'),
                                Forms\Components\TextInput::make('contact_map_url')
                                    ->label('Lien Google Maps')
                                    ->url(),
                            ]),

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

                        Forms\Components\Tabs\Tab::make('Réseaux sociaux')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\TextInput::make('social_facebook')
                                    ->label('Facebook')
                                    ->url()
                                    ->prefix('https://'),
                                Forms\Components\TextInput::make('social_instagram')
                                    ->label('Instagram')
                                    ->url()
                                    ->prefix('https://'),
                                Forms\Components\TextInput::make('social_linkedin')
                                    ->label('LinkedIn')
                                    ->url()
                                    ->prefix('https://'),
                                Forms\Components\TextInput::make('social_youtube')
                                    ->label('YouTube')
                                    ->url()
                                    ->prefix('https://'),
                            ]),

                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('seo_title')
                                    ->label('Titre SEO')
                                    ->maxLength(60),
                                Forms\Components\Textarea::make('seo_description')
                                    ->label('Description SEO')
                                    ->maxLength(160)
                                    ->rows(2),
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
