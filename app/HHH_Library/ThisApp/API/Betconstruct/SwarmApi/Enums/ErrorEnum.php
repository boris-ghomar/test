<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums;

use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;

enum ErrorEnum: int implements Translatable
{
    use EnumActions;

        // case = errorcode
    case BadRequest = 1;
    case InvalidCommand = 2;
    case ServiceUnavailable = 3;
    case SessionNotFound = 5;
    case SubscriptionNotFound = 6;
    case NotSubscribed = 7;
    case InvalidLevel = 10;
    case InvalidField = 11;
    case InvalidCredentials = 12;
    case InvalidTreeMode = 13;
    case QuerySyntaxIsInvalid = 14;
    case InvalidRegularExpression = 15;
    case InvalidSource = 16;
    case UnsupportedFormatException = 17;
    case FileSizeException = 18;
    case InsufficientBalance = 20;
    case OperationNotAllowed = 21;
    case LimitReached = 22;
    case TemporaryUnavailable = 23;
    case AbusiveContent = 24;
    case Birth_DateShouldBeProvided = 25;
    case InvalidPromoCode = 26;
    case RecaptchaVerificationNeeded = 27;
    case TokenHasExpired = 28;
    case RecaptchaHasNotVerified = 29;
    case GeoRestricted = 30;
    case HcaptchaHasNotVerified = 31;
    case PaymentServicesIsUnavailable = 50;
    case NoResponseFromDrone = 99;
    case MovedPermanently = 301;


    /**
     * Get error message with case name as key.
     *
     * Use this function when you are not sure whether
     * the existence case is defined or not.
     *
     * @param  ?int $errorCode
     * @param  string $errorMessage
     * @return string
     */
    public static function findMessage(?int $errorCode, string $errorMessage): string
    {
        /** @var self $case */
        $case = is_null($errorCode) ? null : self::getCaseByValue($errorCode);

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
                $errorCode > 0 ? __('bc_api.UnknownPartnerError') : __('bc_api.UnknownError'),
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
            __('bc_api.ApiError', ['errorCode' => $this->value]),
            $translate
        );
    }
}
