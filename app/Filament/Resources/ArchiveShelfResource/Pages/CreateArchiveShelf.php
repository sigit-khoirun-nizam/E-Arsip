<?php

namespace App\Filament\Resources\ArchiveShelfResource\Pages;

use App\Filament\Resources\ArchiveShelfResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArchiveShelf extends CreateRecord
{
    protected static string $resource = ArchiveShelfResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
