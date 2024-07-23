<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums;

use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;

enum ApiConfigEnum: string
{
    /**
     * Betconstruct External Admin API 2.5
     *
     */

    /**
     * IMPORTANT NOTE:
     * For access values, instead of using ->value use ->getValue() function
     */

    case ApiName = "Betconstruct External Admin API";

    case HashAlgorithm = "SHA256";
        // case ApiUrl = "http://agp-externaladmin.betconstruct.com/api/en/";
    case ApiUrl = "https://agp-externaladmin.bcapps.net/api/en/";
    case PartnerId = "934"; // Betcart ID
    case HashKey = "B9wSbKaubu8tCB90Q";
    case RequestMethod = HttpMethodEnum::POST->name;


    /**
     * Required Headers
     *
     * @return array
     */
    public static function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        $settingCaseName = AppTechnicalSettingsEnum::BC_EXTERNAL_ADMIN_PREFIX . $this->name;

        /** @var AppTechnicalSettingsEnum $settingCase */

        if ($settingCase = AppTechnicalSettingsEnum::getCase($settingCaseName))
            return $settingCase->getValue($this->value);

        return $this->value;
    }
}
