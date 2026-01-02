<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatbotFlowResource\Pages;
use App\Models\ChatbotFlow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChatbotFlowResource extends Resource
{
    protected static ?string $model = ChatbotFlow::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $navigationLabel = 'Chatbot';

    protected static ?string $modelLabel = 'Réponse Chatbot';

    protected static ?string $pluralModelLabel = 'Réponses Chatbot';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('trigger')
                            ->label('Déclencheur')
                            ->required()
                            ->helperText('Identifiant unique: welcome, services, quote, contact, etc.')
                            ->placeholder('welcome'),
                        Forms\Components\Select::make('trigger_type')
                            ->label('Type de déclencheur')
                            ->options([
                                'button' => 'Bouton (clic utilisateur)',
                                'keyword' => 'Mot-clé (texte utilisateur)',
                                'fallback' => 'Fallback (réponse par défaut)',
                            ])
                            ->default('button')
                            ->required(),
                        Forms\Components\TagsInput::make('keywords')
                            ->label('Mots-clés')
                            ->helperText('Mots qui déclenchent cette réponse (pour type "mot-clé")')
                            ->placeholder('Ajoutez un mot-clé')
                            ->visible(fn (callable $get) => $get('trigger_type') === 'keyword'),
                        Forms\Components\Select::make('parent_id')
                            ->label('Réponse parente')
                            ->relationship('parent', 'trigger')
                            ->searchable()
                            ->preload()
                            ->helperText('Optionnel: pour créer des sous-menus'),
                        Forms\Components\TextInput::make('order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Message')
                    ->description('Le message affiché par le chatbot')
                    ->schema([
                        Forms\Components\Textarea::make('message.fr')
                            ->label('Français')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('message.en')
                            ->label('Anglais')
                            ->rows(3),
                        Forms\Components\Textarea::make('message.ar')
                            ->label('Arabe')
                            ->rows(3),
                    ])->columns(3),

                Forms\Components\Section::make('Réponses rapides')
                    ->description('Boutons affichés après le message')
                    ->schema([
                        Forms\Components\Repeater::make('quick_replies')
                            ->label('')
                            ->schema([
                                Forms\Components\TextInput::make('text.fr')
                                    ->label('Texte (FR)')
                                    ->required(),
                                Forms\Components\TextInput::make('text.en')
                                    ->label('Texte (EN)'),
                                Forms\Components\TextInput::make('text.ar')
                                    ->label('Texte (AR)'),
                                Forms\Components\TextInput::make('action')
                                    ->label('Action')
                                    ->helperText('flow:welcome, faq:1, url:https://...')
                                    ->required(),
                            ])
                            ->columns(4)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['text']['fr'] ?? null),
                    ]),

                Forms\Components\Section::make('Action automatique')
                    ->description('Action exécutée automatiquement (optionnel)')
                    ->schema([
                        Forms\Components\Select::make('action')
                            ->label('Type d\'action')
                            ->options([
                                'url' => 'Ouvrir une URL',
                                'whatsapp' => 'Ouvrir WhatsApp',
                                'contact' => 'Rediriger vers Contact',
                            ]),
                        Forms\Components\TextInput::make('action_value')
                            ->label('Valeur')
                            ->helperText('URL ou numéro de téléphone'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trigger')
                    ->label('Déclencheur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('trigger_type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'button',
                        'success' => 'keyword',
                        'warning' => 'fallback',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'button' => 'Bouton',
                        'keyword' => 'Mot-clé',
                        'fallback' => 'Fallback',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('message.fr')
                    ->label('Message (FR)')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.trigger')
                    ->label('Parent')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('trigger_type')
                    ->label('Type')
                    ->options([
                        'button' => 'Bouton',
                        'keyword' => 'Mot-clé',
                        'fallback' => 'Fallback',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),
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
            ->defaultSort('order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatbotFlows::route('/'),
            'create' => Pages\CreateChatbotFlow::route('/create'),
            'edit' => Pages\EditChatbotFlow::route('/{record}/edit'),
        ];
    }
}
