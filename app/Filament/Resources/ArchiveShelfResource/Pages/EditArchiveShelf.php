<?php

namespace App\Filament\Resources\ArchiveShelfResource\Pages;

use App\Filament\Resources\ArchiveShelfResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArchiveShelf extends EditRecord
{
    protected static string $resource = ArchiveShelfResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
