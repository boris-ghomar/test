<?php

namespace App\Interfaces;

use App\HHH_Library\general\php\Enums\LocaleEnum;

interface Translatable
{

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string;

    /* Sample*/
    /*
    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    /*
    public function translate(LocaleEnum $locale = null): ?string
    {
        return match ($this) {
            self::Gregorian => __('general.CalendarType.Gregorian'),
            self::Persian   => __('general.CalendarType.Persian'),

            default => ''
        };
    }
    */
}
