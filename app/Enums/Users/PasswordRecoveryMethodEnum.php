<?php

namespace App\Enums\Users;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum PasswordRecoveryMethodEnum implements Translatable
{
    use EnumActions;

    case Email;
    case Mobile;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        $locale = is_null($locale) ? null : $locale->value;

        return match ($this) {
            self::Email     => __('general.Email', [], $locale),
            self::Mobile    => __('general.Mobile', [], $locale),

            default => __('general.unknown', [], $locale)
        };
    }
}
