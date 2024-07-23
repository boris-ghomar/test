<?php

namespace App\HHH_Library\ThisApp\API\JustCall\Enums;

use App\HHH_Library\general\php\ArrayHelper;
use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum ErrorEnum implements Translatable
{
    use EnumActions;

    case IncorrectNumber;
    case UsaRegionRestriction;
    case TryingToSendLandline;

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
    public static function findMessage(string $errorMessage): string
    {
        $errorCaseName = ArrayHelper::searchInsensitiveCase($errorMessage, __('JustCallApi.errorKeys'));

        /** @var self $case */
        $case = self::getCase($errorCaseName);

        if (!is_null($case)) {
            return $case->translate();
        } else {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                sprintf("Error not found.\nError Message: %s", $errorMessage),
                ApiConfigEnum::ApiName->getValue()
            );

            return __('JustCallApi.UnknownPartnerError');
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
        $langKey = 'JustCallApi.' . $this->name;
        $translate = __($langKey);

        // Translation not definded
        if ($translate == $langKey)
            $translate = __('JustCallApi.UnknownPartnerError');

        return $translate;
    }
}
