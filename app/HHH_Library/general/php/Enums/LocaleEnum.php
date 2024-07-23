<?php

namespace App\HHH_Library\general\php\Enums;

use App\Interfaces\Translatable;
use App\Enums\Session\LocaleKeyEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use Illuminate\Support\Facades\Session;

enum LocaleEnum: string implements Translatable
{
    /**
     * Source : https://saimana.com/list-of-country-locale-code/
     */

    use EnumActions;

    case English = "en"; // General English
    case Persian = "fa";

    /********************* static methods *********************/

    /**
     * Get locale case from session
     *
     * @param  \App\Enums\Session\LocaleKeyEnum $localeKey
     * @param  string $default
     * @return self
     */
    public static function getSessionLocale(LocaleKeyEnum $localeKey = null, string $default = null): self
    {
        if (is_null($default))
            $default = config('app.locale');

        if (is_null($localeKey))
            $localeKey = LocaleKeyEnum::getRouteSessionKey();

        $locale = Session::get($localeKey->value, $default);

        return self::hasValue($locale) ? self::getCaseByValue($locale) : self::English;
    }
    /********************* static methods END *********************/

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

            self::English       => __('general.locale.LangName.en', [], $locale),
            self::Persian       => __('general.locale.LangName.fa', [], $locale),

            default => null
        };
    }

    /**
     * Get translated array
     *
     * @override \App\HHH_Library\general\php\traits\Enums\EnumToArray
     *
     * @return array
     */
    public static function translatedArray(): array
    {
        $translatedArray = [];

        foreach (self::cases() as $case) {

            $translatedArray[$case->translate()] = $case->name;
        }
        return $translatedArray;
    }

    /**
     * GEt direction of locale
     *
     * @return ?string
     */
    public function direction(): ?string
    {
        return match ($this) {
            self::English       => "ltr",
            self::Persian       => "rtl",

            default => null
        };
    }

    /**
     * Get locale iso code 3-Digit format
     *
     * @return ?string
     */
    public function isoCode3Dig(): ?string
    {
        return match ($this) {

            self::English       => "eng",
            self::Persian       => "fas",

            default => null
        };
    }
}
