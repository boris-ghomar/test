<?php

namespace App\HHH_Library\general\php\Enums;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum CountryEnum implements Translatable
{
    use EnumActions;

    case UnitedKingdom;
    case Iran;

    /********************* static methods *********************/

    /**
     * Get all avaliable locales
     *
     * @return array
     */
    public static function locales(): array
    {
        $locales = [];
        foreach (self::cases() as $case) {

            array_push($locales, $case->locale());
        }

        return $locales;
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
            self::UnitedKingdom     => __('countries.UnitedKingdom', [], $locale),
            self::Iran              => __('countries.Iran', [], $locale),

            default => null
        };
    }


    /**
     * Get country locale Enum objemct
     *
     *
     * @return App\HHH_Library\general\php\Enums\LocaleEnum|null
     */
    public function localeEnum(): LocaleEnum|null
    {
        return match ($this) {
            self::UnitedKingdom     => LocaleEnum::English,
            self::Iran              => LocaleEnum::Persian,

            default => null
        };
    }

    /**
     * Get language of country
     *
     * @param  \App\HHH_Library\general\php\Enums\LocaleEnum $locale : (optional) You can force to use desire locale for display
     * @return ?string
     */
    public function language(LocaleEnum $locale = null): ?string
    {

        /** @var localeEnum $localeEnum  */
        $localeEnum = $this->localeEnum();

        return $localeEnum->translate($locale);
    }



    /**
     * Get country locale code
     *
     *
     * @return ?string
     */
    public function locale(): ?string
    {
        /** @var localeEnum $localeEnum  */
        $localeEnum = $this->localeEnum();

        return is_null($localeEnum) ? null : $localeEnum->value;
    }



    /**
     * GEt direction of locale
     *
     * @return ?string
     */
    public function direction(): ?string
    {
        /** @var localeEnum $localeEnum  */
        $localeEnum = $this->localeEnum();

        return is_null($localeEnum) ? null : $localeEnum->direction();
    }

    /**
     * Get the name of the country in alpha2Code format
     *
     * @return ?string
     */
    public function alpha2DigCode(): ?string
    {
        return match ($this) {
            self::UnitedKingdom     => "gb",
            self::Iran              => "ir",

            default => null
        };
    }

    /**
     * Get the timezone of the country
     *
     * @param bool $daylightSaving
     * @return ?string
     */
    public function timezone(bool $daylightSaving = true): ?string
    {
        return match ($this) {
            self::UnitedKingdom     => $daylightSaving ? "+00:00" : "+00:00",
            self::Iran              => $daylightSaving ? "+3:30" : "+3:30",

            default => null
        };
    }
}
