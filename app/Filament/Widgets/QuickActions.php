<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static ?int $sort = 3;

    protected static string $view = 'filament.widgets.quick-actions';

    protected int | string | array $columnSpan = [
        'default' => 12,
        'md' => 6,
        'lg' => 4,
    ];

    public function getActions(): array
    {
        return [
            [
                'label' => 'Buat Surat Keluar',
                'icon' => 'heroicon-o-paper-airplane',
                'color' => 'amber',
                'url' => \App\Filament\Resources\OutgoingLetterResource::getUrl('create'),
            ],
            [
                'label' => 'Upload Arsip Baru',
                'icon' => 'heroicon-o-arrow-up-tray',
                'color' => 'blue',
                'url' => \App\Filament\Resources\ArchiveResource::getUrl('create'),
            ],
            [
                'label' => 'Kelola Ordner',
                'icon' => 'heroicon-o-folder',
                'color' => 'emerald',
                'url' => \App\Filament\Resources\OrdnerResource::getUrl('index'),
            ],
        ];
    }
}
