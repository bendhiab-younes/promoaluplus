<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectTypeResource\Pages;
use App\Models\ProjectType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectTypeResource extends Resource
{
    protected static ?string $model = ProjectType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $modelLabel = 'Type de projet';

    protected static ?string $pluralModelLabel = 'Types de projet';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Nom (multilingue)')
                ->schema([
                    Forms\Components\TextInput::make('name.fr')
                        ->label('🇫🇷 Français')
                        ->rules(['required', 'string', 'max:60'])
                        ->markAsRequired(),
                    Forms\Components\TextInput::make('name.en')
                        ->label('🇬🇧 English')
                        ->maxLength(60),
                    Forms\Components\TextInput::make('name.ar')
                        ->label('🇸🇦 العربية')
                        ->maxLength(60)
                        ->extraInputAttributes(['dir' => 'rtl']),
                ])->columns(3),

            Forms\Components\Section::make('Paramètres')
                ->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('Identifiant unique (slug)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->alphaDash()
                        ->maxLength(50)
                        ->helperText('Utilisé dans l\'URL de filtrage des réalisations.'),
                    Forms\Components\Select::make('color')
                        ->label('Couleur du badge')
                        ->options([
                            'info' => 'Bleu',
                            'warning' => 'Orange',
                            'success' => 'Vert',
                            'danger' => 'Rouge',
                            'gray' => 'Gris',
                        ])
                        ->default('info')
                        ->native(false),
                    Forms\Components\TextInput::make('icon')
                        ->label('Icône')
                        ->maxLength(50)
                        ->helperText('Nom d\'icône Lucide, par ex. app-window, door-open.'),
                    Forms\Components\TextInput::make('order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),
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
                Tables\Columns\TextColumn::make('name.fr')
                    ->label('Nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Actif'),
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
            ->reorderable('order');
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
            'index' => Pages\ListProjectTypes::route('/'),
            'create' => Pages\CreateProjectType::route('/create'),
            'edit' => Pages\EditProjectType::route('/{record}/edit'),
        ];
    }
}
