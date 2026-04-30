<?php

namespace App\Filament\Admin\Themes;

use Filament\Panel;
use Hasnayeen\Themes\Contracts\CanModifyPanelConfig;
use Hasnayeen\Themes\Contracts\Theme;

class Light implements CanModifyPanelConfig, Theme
{
    public static function getName(): string
    {
        return 'light';
    }

    public static function getPath(): string
    {
        return 'resources/css/filament/admin/themes/light.css';
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
