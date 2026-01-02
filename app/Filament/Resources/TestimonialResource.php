<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Filament\Resources\TestimonialResource\RelationManagers;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationGroup = 'Contenu';
    
    protected static ?string $modelLabel = 'Témoignage';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Client')
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label('Nom du client')
                            ->required(),
                        Forms\Components\TextInput::make('client_location')
                            ->label('Localisation')
                            ->placeholder('Paris, France'),
                        Forms\Components\Select::make('project_type')
                            ->label('Type de projet')
                            ->options([
                                'windows' => 'Fenêtres',
                                'doors' => 'Portes',
                                'facades' => 'Façades',
                                'veranda' => 'Véranda',
                            ]),
                        Forms\Components\Select::make('rating')
                            ->label('Note')
                            ->options([
                                5 => '⭐⭐⭐⭐⭐ (5/5)',
                                4 => '⭐⭐⭐⭐ (4/5)',
                                3 => '⭐⭐⭐ (3/5)',
                            ])
                            ->default(5),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Témoignage (multilingue)')
                    ->schema([
                        Forms\Components\Textarea::make('content.fr')
                            ->label('Français')
                            ->required(),
                        Forms\Components\Textarea::make('content.en')
                            ->label('English'),
                        Forms\Components\Textarea::make('content.ar')
                            ->label('عربي'),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Paramètres')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client_location')
                    ->label('Lieu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Note')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state)),
                Tables\Columns\TextColumn::make('project_type')
                    ->label('Type')
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
