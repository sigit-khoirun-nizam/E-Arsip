<?php

namespace App\Filament\Resources\ArchiveRackResource\Pages;

use App\Filament\Resources\ArchiveRackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArchiveRack extends EditRecord
{
    protected static string $resource = ArchiveRackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
