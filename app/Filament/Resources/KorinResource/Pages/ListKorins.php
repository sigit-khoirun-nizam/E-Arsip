<?php

namespace App\Filament\Resources\KorinResource\Pages;

use App\Filament\Resources\KorinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListKorins extends ListRecords
{
    protected static string $resource = KorinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Surat'),
            'belum_disposisi' => Tab::make('Belum Disposisi')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDoesntHave('disposisis'))
                ->badge(\App\Models\Korin::whereDoesntHave('disposisis')->count()),
            'sudah_disposisi' => Tab::make('Sudah Disposisi')
                ->modifyQueryUsing(fn(Builder $query) => $query->has('disposisis')),
        ];
    }
}
