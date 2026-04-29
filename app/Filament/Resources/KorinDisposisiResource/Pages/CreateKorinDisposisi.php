<?php

namespace App\Filament\Resources\KorinDisposisiResource\Pages;

use App\Filament\Resources\KorinDisposisiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKorinDisposisi extends CreateRecord
{
    protected static string $resource = KorinDisposisiResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

