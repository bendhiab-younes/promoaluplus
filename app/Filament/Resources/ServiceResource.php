<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    
    protected static ?string $navigationGroup = 'Contenu';
    
    protected static ?string $modelLabel = 'Service';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identification')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icône Lucide')
                            ->placeholder('home, door-open, building'),
                        Forms\Components\Select::make('color')
                            ->label('Couleur')
                            ->options([
                                'blue' => 'Bleu',
                                'orange' => 'Orange',
                                'green' => 'Vert',
                                'purple' => 'Violet',
                            ])
                            ->default('blue'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0),
                    ])->columns(4),
                    
                Forms\Components\Section::make('Titre (multilingue)')
                    ->schema([
                        Forms\Components\TextInput::make('title.fr')
                            ->label('Français')
                            ->required(),
                        Forms\Components\TextInput::make('title.en')
                            ->label('English'),
                        Forms\Components\TextInput::make('title.ar')
                            ->label('عربي'),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Description courte (multilingue)')
                    ->schema([
                        Forms\Components\Textarea::make('short_description.fr')
                            ->label('Français'),
                        Forms\Components\Textarea::make('short_description.en')
                            ->label('English'),
                        Forms\Components\Textarea::make('short_description.ar')
                            ->label('عربي'),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Caractéristiques')
                    ->schema([
                        Forms\Components\TagsInput::make('features')
                            ->label('Liste des caractéristiques')
                            ->placeholder('Ajouter une caractéristique'),
                    ]),
                    
                Forms\Components\Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title.fr')
                    ->label('Service')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->label('Couleur')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
