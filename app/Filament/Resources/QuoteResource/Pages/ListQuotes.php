<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouveau devis')
                ->icon('heroicon-m-plus'),
        ];
    }

    /**
     * One tab per stage of the devis pipeline, so the list answers "what do I
     * have to do next?" instead of showing every devis ever written at once.
     */
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Tous')
                ->icon('heroicon-m-queue-list')
                ->badge(Quote::query()->count()),
        ];

        foreach (QuoteResource::STATUSES as $status => $meta) {
            $count = Quote::query()->where('status', $status)->count();

            $tabs[$status] = Tab::make($meta['label'])
                ->icon($meta['icon'])
                ->badge($count ?: null)
                ->badgeColor($meta['color'])
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', $status));
        }

        return $tabs;
    }
}
