<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestQuotes extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Dernières demandes de devis';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Quote::query()
                    ->select(['id', 'first_name', 'name', 'phone', 'project_type', 'status', 'created_at'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Client')
                    ->formatStateUsing(fn (Quote $record): string => $record->full_name)
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone'),
                Tables\Columns\TextColumn::make('project_type')
                    ->label('Type')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => Quote::projectTypeLabel($state, 'fr')),
                Tables\Columns\TextColumn::make('status')
                    ->label('Étape')
                    ->badge()
                    ->icon(fn (string $state): ?string => QuoteResource::statusIcon($state))
                    ->formatStateUsing(fn (string $state): string => QuoteResource::statusLabel($state))
                    ->color(fn (string $state): string => QuoteResource::statusColor($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->url(fn (Quote $record): string => QuoteResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-m-eye'),
            ])
            ->paginated(false);
    }
}
