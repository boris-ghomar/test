<?php

namespace App\HHH_Library\general\php\Enums;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum CalendarTypeEnum implements Translatable
{
    use EnumActions;

    case Gregorian;
    case Persian;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return match ($this) {
            self::Gregorian => __('general.CalendarType.Gregorian'),
            self::Persian   => __('general.CalendarType.Persian'),

            default => ''
        };
    }

    /**
     * Get default date format
     *
     * @return string
     */
    public function defaultDateFormat(): string
    {
        return match ($this) {
            self::Gregorian => "Y-m-d",
            self::Persian   => "Y/m/d",

            default => self::Gregorian->defaultDateFormat()
        };
    }

    /**
     * Get user guide date format
     * For show to user as example input, not for technichal usage.
     *
     * @return string
     */
    public function userGuideDateFormat(): string
    {
        return match ($this) {
            self::Gregorian => "yyyy-mm-dd",
            self::Persian   => "yyyy/mm/dd",

            default => self::Gregorian->defaultDateFormat()
        };
    }

    /**
     * Get default date-time format
     *
     * @return string
     */
    public function defaultDateTimeFormat(): string
    {
        return match ($this) {
            self::Gregorian => sprintf("%s H:i:s", $this->defaultDateFormat()),
            self::Persian   => sprintf("%s H:i:s", $this->defaultDateFormat()),

            default => self::Gregorian->defaultDateTimeFormat()
        };
    }

    /**
     * Get date preg pattern for case
     *
     * @return \App\HHH_Library\general\php\Enums\PregPatternValidationEnum
     */
    public function datePregPattern(): PregPatternValidationEnum
    {
        return match ($this) {
            self::Gregorian => PregPatternValidationEnum::GregorianDate,
            self::Persian   => PregPatternValidationEnum::PersianDate,

            default => self::Gregorian->datePregPattern()
        };
    }
}
