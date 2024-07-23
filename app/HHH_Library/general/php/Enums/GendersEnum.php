<?php

namespace App\HHH_Library\general\php\Enums;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum GendersEnum implements Translatable
{
    use EnumActions;

    case Male;
    case Female;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return match ($this) {
            self::Male       => __('general.GenderData.male'),
            self::Female     => __('general.GenderData.female'),

            default => __('general.GenderData.other')
        };
    }
}
