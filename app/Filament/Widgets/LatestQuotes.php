<?php

namespace App\Filament\Widgets;

use App\Models\Quote;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestQuotes extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Dernières demandes de devis';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Quote::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone'),
                Tables\Columns\TextColumn::make('project_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'windows' => 'Fenêtres',
                        'doors' => 'Portes',
                        'curtains' => 'Rideaux',
                        'railings' => 'Garde-corps',
                        'pergola' => 'Pergola',
                        'kitchen' => 'Cuisine',
                        'shelter' => 'Abri',
                        'shutters' => 'Volets',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'windows' => 'info',
                        'doors' => 'warning',
                        'curtains' => 'gray',
                        'railings' => 'success',
                        'pergola' => 'success',
                        'kitchen' => 'warning',
                        'shelter' => 'info',
                        'shutters' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Nouveau',
                        'contacted' => 'Contacté',
                        'quoted' => 'Devis envoyé',
                        'accepted' => 'Accepté',
                        'rejected' => 'Refusé',
                        'completed' => 'Terminé',
                        default => $state,
                    })
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
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->url(fn (Quote $record): string => route('filament.admin.resources.quotes.edit', $record))
                    ->icon('heroicon-m-eye'),
            ])
            ->paginated(false);
    }
}
