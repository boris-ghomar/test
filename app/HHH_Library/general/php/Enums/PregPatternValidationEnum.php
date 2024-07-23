<?php

namespace App\HHH_Library\general\php\Enums;

use App\HHH_Library\general\php\traits\Enums\EnumActions;
use Illuminate\Support\Facades\Validator;

enum PregPatternValidationEnum: string
{

    use EnumActions;

        // Validations
    case Hour24                         = "/^(([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?)$/"; // 24-hourFormat Exmp: 23:05|10:25:31
    case Timezone                       = "/^(?:[+-](?:2[0-3]|[01][0-9]):[0-5][0-9])$/";
    case EnglishString                  = '/[^A-Za-z0-9_.()\/\- ]/';
    case EnglishStringUsernameFormat    = '/[^A-Za-z0-9_.]/';
    case MinOneLowercase                = '(^(?=.*[a-z]).+$)';
    case MinOneUppercase                = '(^(?=.*[A-Z]).+$)';
    case MinOneNumber                   = '(^(?=.*\d).+$)';
    case MinOneSpecialCharacter         = '(^(?=.*?[#?!@$%^&*-]).+$)';
    case PersianString                  = '/[^\x{0590}-\x{05ff}\x{0600}-\x{06ff} .]/u';
    case GregorianDate                  = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'; // YYYY-MM-DD
    case PersianDate                    = '/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])$/'; // YYYY/MM/DD
    case Url                            = '/^https?:\\/\\/(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)$/';
    case Websocket                      = '/^wss?:\\/\\/(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)$/';
    case MobileNumber                   = '/^[0]{2}[1-9]{1}[0-9]{5,12}$/'; // Exmp: 00971501231234


    /**
     * Get how to validate preg_match
     *
     * @return bool
     *                  true: The field under validation must match the given regular expression
     *                  false: The field under validation must not match the given regular expression
     */
    private function mustMatch(): bool
    {
        return match ($this) {

            self::Hour24                        => true,
            self::Timezone                      => true,
            self::MinOneLowercase               => true,
            self::MinOneUppercase               => true,
            self::MinOneNumber                  => true,
            self::MinOneSpecialCharacter        => true,
            self::GregorianDate                 => true,
            self::PersianDate                   => true,
            self::Url                           => true,
            self::Websocket                     => true,
            self::MobileNumber                  => true,

            self::EnglishString                 => false,
            self::EnglishStringUsernameFormat   => false,
            self::PersianString                 => false,
        };
    }

    /**
     * Get the error related to the failure of the case
     *
     * @param  ?array $replace
     * @return string
     */
    public function error(?array $replace = null): string
    {
        if (is_null($replace))
            $replace = [];

        return match ($this) {

            self::Hour24                        => __('validation.custom.DateTimeFormat.IncorrectTimeFormat'),
            self::Timezone                      => __('validation.timezone'),
            self::EnglishString                 => __('validation.custom.EnglishString.EnglishString', $replace),
            self::EnglishStringUsernameFormat   => __('validation.custom.EnglishString.EnglishStringUsernameFormat', $replace),
            self::MinOneLowercase               => __('validation.custom.String.MinOneLowercase', $replace),
            self::MinOneUppercase               => __('validation.custom.String.MinOneUppercase', $replace),
            self::MinOneNumber                  => __('validation.custom.String.MinOneNumber', $replace),
            self::MinOneSpecialCharacter        => __('validation.custom.String.MinOneSpecialCharacter', $replace),
            self::PersianString                 => __('validation.custom.PersianString.PersianString', $replace),
            self::GregorianDate                 => __('validation.custom.dateFormat.IncorrectDateFormat', $replace),
            self::PersianDate                   => __('validation.custom.dateFormat.IncorrectDateFormat', $replace),
            self::Url                           => __('validation.custom.web.Url', $replace),
            self::Websocket                     => __('validation.custom.web.Websocket', $replace),
            self::MobileNumber                  => __('validation.custom.number.numberPattern', $replace),

            default => "Error"
        };
    }

    /**
     * Validate with PHP preg_math
     *
     * @param  mixed $value
     * @param  ?string $customPattern : (optinal) If you need to use custom pattern instead of default value, you can use this param. (Exmpale: used in "App\Rules\General\StringPattern\MinOneSpecialCharacter" )
     * @return bool
     */
    public function validate(mixed $value, ?string $customPattern = null): bool
    {
        $match = preg_match(is_null($customPattern) ? $this->value : $customPattern, $value);

        return $this->mustMatch() ? $match : !$match;
    }

    /**
     * Validate with Laravel regex

     @error
     * NOTICE:
     *  PHP validate is more reliable.
     *  Exmample test:
     *  1) In "MinOneLowercase" in persian letters laravel returns wrong answer.
     *  2) In "MinOneNumber" laravel always returns true answer.
     *
     * @param  mixed $value
     * @return bool
     */
    public function laravelValidate(mixed $value): bool
    {
        $validator = Validator::make(
            [$this->name => $value],
            [$this->name => 'required', [$this->regex()]]
        );

        return !$validator->fails();
    }

    /**
     * Get laravel regex format
     * Used for laravel validation rules.
     *
     * @return string
     */
    public function regex(): string
    {
        return sprintf("%s:%s", $this->mustMatch() ? 'regex' : 'not_regex', $this->value);
    }
}
