<?php

namespace App\Filament\Widgets;

use App\Models\Archive;
use App\Models\Korin;
use App\Models\OutgoingLetter;
use App\Models\IncomingLetter;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class RecentActivity extends Widget
{
    protected static ?int $sort = 4;
    protected static bool $shouldRegister = false;

    protected static string $view = 'filament.widgets.recent-activity';

    protected int | string | array $columnSpan = [
        'default' => 12,
        'sm' => 12,
        'md' => 6,
        'lg' => 4,
    ];

    public function getActivities(): Collection
    {
        $user = auth()->user();
        $unitId = $user->unit_id;
        $isSuperAdmin = $user->hasRole('super_admin');

        $archives = Archive::when(!$isSuperAdmin, fn($q) => $q->where('unit_id', $unitId))
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'type' => 'archive',
                'title' => $a->title,
                'description' => 'Dokumen arsip baru',
                'icon' => 'heroicon-o-document-duplicate',
                'color' => 'success',
                'time' => $a->created_at,
            ]);

        $outgoing = OutgoingLetter::when(!$isSuperAdmin, fn($q) => $q->where('unit_id', $unitId))
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn($l) => [
                'type' => 'outgoing',
                'title' => $l->letter_number ?? 'Surat Keluar',
                'description' => $l->subject ?? 'Surat keluar baru',
                'icon' => 'heroicon-o-paper-airplane',
                'color' => 'primary',
                'time' => $l->created_at,
            ]);

        $incoming = IncomingLetter::latest()
            ->limit(3)
            ->get()
            ->map(fn($l) => [
                'type' => 'incoming',
                'title' => $l->letter_number ?? 'Surat Masuk',
                'description' => $l->subject ?? 'Surat masuk baru',
                'icon' => 'heroicon-o-envelope',
                'color' => 'info',
                'time' => $l->created_at,
            ]);

        return $archives->concat($outgoing)->concat($incoming)
            ->sortByDesc('time')
            ->take(8);
    }
}
