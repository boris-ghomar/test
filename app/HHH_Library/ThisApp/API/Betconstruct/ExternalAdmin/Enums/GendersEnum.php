<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum GendersEnum: int implements Translatable
{
    use EnumActions;

    case Unknown    = 0;
    case Male       = 1;
    case Female     = 2;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return match ($this) {

            self::Unknown       => __('general.GenderData.unknown'),
            self::Male          => __('general.GenderData.male'),
            self::Female        => __('general.GenderData.female'),
        };
    }
}
