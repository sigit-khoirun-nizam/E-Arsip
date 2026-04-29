<?php

namespace App\Filament\Resources\ArchiveShelfResource\Pages;

use App\Filament\Resources\ArchiveShelfResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArchiveShelves extends ListRecords
{
    protected static string $resource = ArchiveShelfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
