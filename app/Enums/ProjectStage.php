<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Where a project stands today — rendered as the badge on its card and page. */
enum ProjectStage: string implements HasColor, HasLabel
{
    case Live = 'live';
    case Staging = 'staging';
    case Development = 'development';

    public function getLabel(): string
    {
        return match ($this) {
            self::Live => 'Live in production',
            self::Staging => 'Private staging',
            self::Development => 'In development',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Live => 'success',
            self::Staging => 'warning',
            self::Development => 'gray',
        };
    }
}
