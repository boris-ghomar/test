<?php

namespace App\Enums\Users;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum UsersStatusEnum implements Translatable
{
    use EnumActions;

    case Active;
    case Suspended;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return match ($this) {
            self::Active       => __('general.ClientAccountStatus.active'),
            self::Suspended    => __('general.ClientAccountStatus.suspended'),

            default => __('general.unknown')
        };
    }
}
