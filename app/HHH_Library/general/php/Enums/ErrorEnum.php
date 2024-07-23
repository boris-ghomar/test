<?php

namespace App\HHH_Library\general\php\Enums;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum ErrorEnum: string implements Translatable
{
    use EnumActions;

    case CurlError = "cURL exec error";


    /**
     * Get error message with case name as key.
     *
     * Use this function when you are not sure whether
     * the existence case is defined or not.
     *
     * @param  int $errorCode
     * @param  string $errorMessage
     * @return string
     */
    public static function findMessage(int $error, string $errorMessage): string
    {
        /** @var self $case */
        // search in names
        $case = self::getCase($error);

        // search in values
        if (is_null($case))
            $case = self::getCaseByValue($errorMessage, false);

        if (!is_null($case)) {
            return $case->translate();
        } else {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                sprintf("Error not found.\nError: %s\nError Message: %s", $error, $errorMessage),
                'Internal Unknown Error'
            );

            return __('error.UnknownError');
        }
    }

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        $langKey = 'error.' . $this->name;
        $translate = __($langKey);

        // Translation not definded
        if ($translate == $langKey)
            $translate = __('error.UnknownError');

        return $translate;
    }
}
