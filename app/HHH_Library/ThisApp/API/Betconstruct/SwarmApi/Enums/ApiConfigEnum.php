<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums;

use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;

enum ApiConfigEnum: string
{
    /**
     * Betconstruct External Swarm API
     *
     */

    case ApiName = "Betconstruct Swarm API";

    case ApiUrl = "https://eu-swarm-test.betconstruct.com/";
    case WebSocketUrlAlternative = "wss://eu-swarm-test.betconstruct.com/";
    case WebSocketUrl = "wss://eu-swarm-proxy.pwqbfyjubdrmatch.com/";
    case SiteId = "934"; // Betcart ID

    case RequestMethod = HttpMethodEnum::POST->name;
    case DefaultRequestLanguage = "eng";


    const SESSION_EXPIRE_TIME_SECONDS = 60; // 60 seconds

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
        $settingCaseName = AppTechnicalSettingsEnum::BC_SWARM_API_PREFIX . $this->name;

        /** @var AppTechnicalSettingsEnum $settingCase */
        $settingCase = AppTechnicalSettingsEnum::getCase($settingCaseName);

        if ($settingCase = AppTechnicalSettingsEnum::getCase($settingCaseName))
            return $settingCase->getValue($this->value);

        return $this->value;
    }
}
