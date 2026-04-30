<?php

namespace App\Filament\Widgets;

use App\Models\Archive;
use App\Models\ArchiveBox;
use App\Models\Category;
use App\Models\IncomingLetter;
use App\Models\Korin;
use App\Models\Ordner;
use App\Models\OutgoingLetter;
use App\Models\Unit;
use App\Models\User;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '30s';

    protected int | string | array $columnSpan = [
        'default' => 12,
        'sm' => 12,
        'md' => 6,
        'lg' => 3,
        'xl' => 3,
    ];

    protected function getStats(): array
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin');
        $isSdm = $user->unit && $user->unit->code === '120';
        $unitId = $user->unit_id;

        if ($isSuperAdmin) {
            return [
                Stat::make('Total Dokumen Arsip', new \Illuminate\Support\HtmlString('<span class="text-xl font-bold">' . Archive::count() . '</span>'))
                    ->description('Seluruh arsip dilingkungan unit')
                    ->descriptionIcon('heroicon-m-document-duplicate')
                    ->chart([7, 2, 10, 3, 15, 4, 17])
                    ->color('success'),

                Stat::make('Total Surat Menyurat', new \Illuminate\Support\HtmlString(
                    '<span class="text-xl font-bold">' . (IncomingLetter::count() + OutgoingLetter::count() + Korin::count()) . '</span>'
                ))
                    ->description('Masuk, Keluar, & Korin')
                    ->descriptionIcon('heroicon-m-envelope')
                    ->chart([15, 4, 6, 10, 5, 12, 8])
                    ->color('info'),

                Stat::make('Total Wadah Arsip', new \Illuminate\Support\HtmlString(
                    '<span class="text-base font-bold">' . Ordner::count() . ' Ordner / ' . ArchiveBox::count() . ' Box</span>'
                ))
                    ->description('Ordner & Box Fisik')
                    ->descriptionIcon('heroicon-m-archive-box')
                    ->chart([2, 5, 3, 8, 4, 10, 5])
                    ->color('warning'),

                Stat::make('Ekosistem Pengguna', new \Illuminate\Support\HtmlString(
                    '<span class="text-xl font-bold">' . User::count() . '</span>'
                ))
                    ->description(Unit::count() . ' Unit Organisasi')
                    ->descriptionIcon('heroicon-m-users')
                    ->chart([3, 3, 3, 3, 3, 3, User::count()])
                    ->color('primary'),
            ];
        }

        // Stats for Unit SDM & Umum (code 120)
        if ($isSdm) {
            return [
                Stat::make('Arsip Tersentral', new \Illuminate\Support\HtmlString('<span class="text-xl font-bold">' . Archive::count() . '</span>'))
                    ->description('Total arsip yang dipantau')
                    ->descriptionIcon('heroicon-m-shield-check')
                    ->chart([10, 20, 15, 25, 20, 30, 35])
                    ->color('info'),

                Stat::make('Manajemen Box', new \Illuminate\Support\HtmlString('<span class="text-xl font-bold">' . ArchiveBox::count() . '</span>'))
                    ->description('Box arsip fisik aktif')
                    ->descriptionIcon('heroicon-m-rectangle-group')
                    ->color('success'),

                Stat::make('Total Ordner', new \Illuminate\Support\HtmlString('<span class="text-xl font-bold">' . Ordner::count() . '</span>'))
                    ->description('Semua unit terpantau')
                    ->descriptionIcon('heroicon-m-folder-open')
                    ->chart([5, 8, 4, 10, 6, 12, 15])
                    ->color('warning'),

                Stat::make('Surat Unit SDM', new \Illuminate\Support\HtmlString(
                    '<span class="text-xl font-bold">' . (OutgoingLetter::where('unit_id', $unitId)->count() + IncomingLetter::count()) . '</span>'
                ))
                    ->description('Surat masuk & keluar SDM')
                    ->descriptionIcon('heroicon-m-envelope-open')
                    ->color('primary'),
            ];
        }

        // Normal Unit Stats
        return [
            Stat::make('Dokumen Unit Anda', new \Illuminate\Support\HtmlString('<span class="text-xl font-bold">' . Archive::where('unit_id', $unitId)->count() . '</span>'))
                ->description('Total arsip digital unit')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart([2, 4, 3, 6, 4, 8, 10])
                ->color('success'),

            Stat::make('Ordner Aktif', new \Illuminate\Support\HtmlString('<span class="text-xl font-bold">' . Ordner::where('unit_id', $unitId)->where('status', 'active')->count() . '</span>'))
                ->description('Penyimpanan di unit')
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),

            Stat::make('Surat Keluar Unit', new \Illuminate\Support\HtmlString('<span class="text-xl font-bold">' . OutgoingLetter::where('unit_id', $unitId)->count() . '</span>'))
                ->description('Total surat terkirim')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->chart([5, 2, 8, 3, 10, 4, 12])
                ->color('primary'),
        ];
    }
}
