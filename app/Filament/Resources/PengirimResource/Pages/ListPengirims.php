<?php

namespace App\Filament\Resources\PengirimResource\Pages;

use App\Filament\Resources\PengirimResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengirims extends ListRecords
{
    protected static string $resource = PengirimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
