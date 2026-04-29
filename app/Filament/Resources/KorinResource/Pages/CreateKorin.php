<?php

namespace App\Filament\Resources\KorinResource\Pages;

use App\Filament\Resources\KorinResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKorin extends CreateRecord
{
    protected static string $resource = KorinResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

