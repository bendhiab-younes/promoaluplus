<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Demandes';
    
    protected static ?string $modelLabel = 'Demande de devis';
    
    protected static ?string $pluralModelLabel = 'Demandes de devis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations client')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->required(),
                        Forms\Components\TextInput::make('country')
                            ->label('Pays'),
                        Forms\Components\TextInput::make('city')
                            ->label('Ville'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Détails du projet')
                    ->schema([
                        Forms\Components\Select::make('project_type')
                            ->label('Type de projet')
                            ->options([
                                'windows' => 'Fenêtres',
                                'doors' => 'Portes',
                                'facades' => 'Façades',
                                'veranda' => 'Véranda',
                                'other' => 'Autre',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('budget_range')
                            ->label('Budget'),
                        Forms\Components\TextInput::make('timeline')
                            ->label('Délai'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Gestion')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'new' => '🆕 Nouveau',
                                'contacted' => '📞 Contacté',
                                'quoted' => '📋 Devis envoyé',
                                'accepted' => '✅ Accepté',
                                'rejected' => '❌ Refusé',
                                'completed' => '🎉 Terminé',
                            ])
                            ->required()
                            ->default('new'),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Notes internes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'windows' => 'info',
                        'doors' => 'warning',
                        'facades' => 'success',
                        'veranda' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'danger',
                        'contacted' => 'warning',
                        'quoted' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'gray',
                        'completed' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'Nouveau',
                        'contacted' => 'Contacté',
                        'quoted' => 'Devis envoyé',
                        'accepted' => 'Accepté',
                        'rejected' => 'Refusé',
                        'completed' => 'Terminé',
                    ]),
                Tables\Filters\SelectFilter::make('project_type')
                    ->label('Type de projet')
                    ->options([
                        'windows' => 'Fenêtres',
                        'doors' => 'Portes',
                        'facades' => 'Façades',
                        'veranda' => 'Véranda',
                        'other' => 'Autre',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
