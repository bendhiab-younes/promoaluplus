<?php

namespace App\Filament\Resources\ChatbotFlowResource\Pages;

use App\Filament\Resources\ChatbotFlowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatbotFlows extends ListRecords
{
    protected static string $resource = ChatbotFlowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
