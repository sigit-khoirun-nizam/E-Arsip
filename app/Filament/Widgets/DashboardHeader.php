<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardHeader extends Widget
{
    protected static ?int $sort = 1;

    protected static string $view = 'filament.widgets.dashboard-header';

    protected int | string | array $columnSpan = 'full';

    public function getUserName(): string
    {
        return auth()->user()->name;
    }

    public function getUnitName(): string
    {
        return auth()->user()->unit?->name ?? 'Administrator';
    }
}
