<?php

namespace App\Filament\Resources\PropertyPostResource\Pages;

use App\Filament\Resources\PropertyPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyPost extends EditRecord
{
    protected static string $resource = PropertyPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
