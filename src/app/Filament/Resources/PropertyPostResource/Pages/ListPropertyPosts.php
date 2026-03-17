<?php

namespace App\Filament\Resources\PropertyPostResource\Pages;

use App\Filament\Resources\PropertyPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyPosts extends ListRecords
{
    protected static string $resource = PropertyPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
