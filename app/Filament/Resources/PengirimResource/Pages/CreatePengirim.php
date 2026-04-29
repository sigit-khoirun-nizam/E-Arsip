<?php

namespace App\Filament\Resources\PengirimResource\Pages;

use App\Filament\Resources\PengirimResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePengirim extends CreateRecord
{
    protected static string $resource = PengirimResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

