<?php

namespace App\Filament\Resources\KorinResource\Pages;

use App\Filament\Resources\KorinResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKorin extends ViewRecord
{
    protected static string $resource = KorinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn(\App\Models\Korin $record): string => route('korins.print', $record->id))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
        ];
    }
}
