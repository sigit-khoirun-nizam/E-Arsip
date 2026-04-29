<?php

namespace App\Filament\Resources\OrdnerResource\Pages;

use App\Filament\Resources\OrdnerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrdner extends EditRecord
{
    protected static string $resource = OrdnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
