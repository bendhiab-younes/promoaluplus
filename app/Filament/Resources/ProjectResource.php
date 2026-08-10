<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $modelLabel = 'Projet';

    protected static ?string $pluralModelLabel = 'Projets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Titre (multilingue)')
                    ->schema([
                        Forms\Components\TextInput::make('title.fr')
                            ->label('Titre (Français)')
                            ->required(),
                        Forms\Components\TextInput::make('title.en')
                            ->label('Title (English)'),
                        Forms\Components\TextInput::make('title.ar')
                            ->label('العنوان (عربي)'),
                    ])->columns(3),

                Forms\Components\Section::make('Description (multilingue)')
                    ->schema([
                        Forms\Components\Textarea::make('description.fr')
                            ->label('Description (Français)'),
                        Forms\Components\Textarea::make('description.en')
                            ->label('Description (English)'),
                        Forms\Components\Textarea::make('description.ar')
                            ->label('الوصف (عربي)'),
                    ])->columns(3),

                Forms\Components\Section::make('Détails')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->label('Catégorie')
                            ->options(fn (): array => \App\Models\ProjectType::query()
                                ->active()
                                ->ordered()
                                ->get()
                                ->mapWithKeys(fn (\App\Models\ProjectType $type): array => [$type->slug => $type->getTranslatedName('fr')])
                                ->all())
                            ->required()
                            ->native(false)
                            ->helperText('Gérez la liste dans Contenu → Types de projet.'),
                        Forms\Components\TextInput::make('location')
                            ->label('Emplacement'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Forms\Components\Section::make('Images')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image principale')
                            ->disk('uploads')
                            ->directory('projects')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight('150')
                            ->maxSize(5120),
                        Forms\Components\TextInput::make('image_url')
                            ->label('URL externe (optionnel)')
                            ->maxLength(2048)
                            ->rules(['nullable', 'string', 'max:2048'])
                            ->helperText('Utilisée uniquement si aucun fichier n\'est téléversé.'),
                        Forms\Components\FileUpload::make('gallery')
                            ->label('Galerie')
                            ->disk('uploads')
                            ->directory('projects')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->imagePreviewHeight('120')
                            ->maxSize(5120)
                            ->columnSpanFull()
                            ->helperText('Glissez-déposez les vignettes pour changer leur ordre.'),
                    ])->columns(2),

                Forms\Components\Section::make('Paramètres')
                    ->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Mis en avant'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->getStateUsing(fn (Project $record): ?string => $record->imageSrc()),
                Tables\Columns\TextColumn::make('title.fr')
                    ->label('Titre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => \App\Models\ProjectType::query()
                        ->where('slug', $state)
                        ->first()?->getTranslatedName('fr') ?? (string) $state)
                    ->color(fn (?string $state): string => \App\Models\ProjectType::query()
                        ->where('slug', $state)
                        ->first()?->color ?? 'gray'),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lieu')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Vedette')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options(fn (): array => \App\Models\ProjectType::query()
                        ->active()
                        ->ordered()
                        ->get()
                        ->mapWithKeys(fn (\App\Models\ProjectType $type): array => [$type->slug => $type->getTranslatedName('fr')])
                        ->all()),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Mis en avant'),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
