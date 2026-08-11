<?php

namespace App\Filament\Resources\ServiceOrderResource\Pages;

use App\Filament\Resources\ServiceOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListServiceOrders extends ListRecords
{
    protected static string $resource = ServiceOrderResource::class;

    /**
     * Sin botón de creación manual: el flujo normal se genera solo
     * (Relevamiento::markAsSubmitted()).
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
