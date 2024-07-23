<?php

namespace App\HHH_Library\ThisApp\API\JustCall\Enums;

use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;

enum ApiConfigEnum: string
{
    /**
     * JustCall VOIP Service Provider API
     *
     * Source:
     * https://justcall.io/developer-docs
     *
     */

    /**
     * IMPORTANT NOTE:
     * For access values, instead of using ->value use ->getValue() function
     */

    case ApiName = "JustCall API";

    case ApiUrl = "https://api.justcall.io/v1/";
    case ApiKey = "Api Key";
    case ApiSecret = "Api Secret";
    case RequestMethod = HttpMethodEnum::POST->name;


    /**
     * Required Headers
     *
     * @return array
     */
    public static function headers(): array
    {
        return [
            // 'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => sprintf("%s:%s", self::ApiKey->getValue(), self::ApiSecret->getValue()),
        ];
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        $settingCaseName = AppTechnicalSettingsEnum::JustCall_API_PREFIX . $this->name;

        /** @var AppTechnicalSettingsEnum $settingCase */

        if ($settingCase = AppTechnicalSettingsEnum::getCase($settingCaseName))
            return $settingCase->getValue($this->value);

        return $this->value;
    }
}
