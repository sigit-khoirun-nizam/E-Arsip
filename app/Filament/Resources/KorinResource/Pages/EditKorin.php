<?php

namespace App\Filament\Resources\KorinResource\Pages;

use App\Filament\Resources\KorinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKorin extends EditRecord
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
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
