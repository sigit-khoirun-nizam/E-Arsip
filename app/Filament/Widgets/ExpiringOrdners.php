<?php

namespace App\Filament\Widgets;

use App\Models\Ordner;
use Filament\Widgets\Widget;

class ExpiringOrdners extends Widget
{
    protected static ?int $sort = 5;
    protected static bool $shouldRegister = false;

    protected static string $view = 'filament.widgets.expiring-ordners';

    protected int | string | array $columnSpan = [
        'default' => 12,
        'sm' => 12,
        'md' => 6,
        'lg' => 4,
    ];

    public function getExpiringOrdners()
    {
        $user = auth()->user();
        $unitId = $user->unit_id;
        $isSuperAdmin = $user->hasRole('super_admin');
        $isSdm = $user->unit && $user->unit->code === '120';

        $query = Ordner::query()
            ->with(['unit', 'category'])
            ->whereNotNull('retention_expires_at')
            ->where('retention_expires_at', '<=', now()->addDays(30));

        if (!$isSuperAdmin && !$isSdm) {
            $query->where('unit_id', $unitId);
        }

        return $query->orderBy('retention_expires_at')
            ->limit(5)
            ->get();
    }

    public function getExpiredOrdners()
    {
        $user = auth()->user();
        $unitId = $user->unit_id;
        $isSuperAdmin = $user->hasRole('super_admin');
        $isSdm = $user->unit && $user->unit->code === '120';

        $query = Ordner::query()
            ->with(['unit', 'category'])
            ->whereNotNull('retention_expires_at')
            ->where('retention_expires_at', '<', now())
            ->whereNull('archive_box_id');

        if (!$isSuperAdmin && !$isSdm) {
            $query->where('unit_id', $unitId);
        }

        return $query->orderBy('retention_expires_at')
            ->limit(5)
            ->get();
    }
}
