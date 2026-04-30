<?php

namespace App\Filament\Admin\Themes;

use Filament\Panel;
use Hasnayeen\Themes\Contracts\CanModifyPanelConfig;
use Hasnayeen\Themes\Contracts\Theme;

class Dark implements CanModifyPanelConfig, Theme
{
    public static function getName(): string
    {
        return 'dark';
    }

    public static function getPath(): string
    {
        return 'resources/css/filament/admin/themes/dark.css';
    }

    public function getThemeColor(): array
    {
        return [
            'primary' => '#ffffff',
        ];
    }

    public function modifyPanelConfig(Panel $panel): Panel
    {
        return $panel
            ->viteTheme($this->getPath());
    }
}
