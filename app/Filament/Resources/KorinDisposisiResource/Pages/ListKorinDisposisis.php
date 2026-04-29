<?php

namespace App\Filament\Resources\KorinDisposisiResource\Pages;

use App\Filament\Resources\KorinDisposisiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKorinDisposisis extends ListRecords
{
    protected static string $resource = KorinDisposisiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
