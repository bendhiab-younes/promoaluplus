<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_site')
                ->label('Voir sur le site')
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->url(route('services'))
                ->openUrlInNewTab(),
            Actions\CreateAction::make()
                ->label('Nouveau service'),
        ];
    }
}
