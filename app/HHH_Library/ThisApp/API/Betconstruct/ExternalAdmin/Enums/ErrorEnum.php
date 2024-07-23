<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum ErrorEnum implements Translatable
{
    use EnumActions;

    case InternalError; // Betconstruct internal error. It means the Betconstruct API does not work.
    case PartnerApiWrongHash;
    case DuplicateIBAN;
    case DuplicateEmail;
    case DuplicateLogin;
    case WrongCurrencyCode;
    case AmountRangeDoesNotValidRange; // Comes from addClientToBonus request


    /**
     * Get error message with case name as key.
     *
     * Use this function when you are not sure whether
     * the existence case is defined or not.
     *
     * @param  string $errorCode
     * @param  string $errorMessage
     * @return string
     */
    public static function findMessage(string $errorCode, string $errorMessage): string
    {
        /** @var self $case */
        $case = self::getCase($errorCode);

        if (!is_null($case)) {
            return $case->translate();
        } else {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                sprintf("Error not found.\nError Code: %s\nError Message: %s", $errorCode, $errorMessage),
                ApiConfigEnum::ApiName->getValue()
            );

            return sprintf(
                "%s: %s",
                __('bc_api.ApiError', ['errorCode' => $errorCode]),
                __('bc_api.UnknownError')
            );
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
        $langKey = 'bc_api.' . $this->name;
        $translate = __($langKey);

        // Translation not definded
        if ($translate == $langKey)
            $translate = __('bc_api.UnknownError');

        return sprintf(
            "%s: %s",
            __('bc_api.ApiError', ['errorCode' => '"' . $this->name . '"']),
            $translate
        );
    }
}
