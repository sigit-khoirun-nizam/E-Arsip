<?php

namespace App\Filament\Resources\OrdnerResource\Pages;

use App\Filament\Resources\OrdnerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrdner extends CreateRecord
{
    protected static string $resource = OrdnerResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

