<?php

namespace App\Filament\Resources\ArchiveShelfResource\Pages;

use App\Filament\Resources\ArchiveShelfResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewArchiveShelf extends ViewRecord
{
    protected static string $resource = ArchiveShelfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
