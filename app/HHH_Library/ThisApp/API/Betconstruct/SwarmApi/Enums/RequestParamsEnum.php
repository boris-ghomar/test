<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums;

use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\general\php\traits\Enums\EnumCastParams;
use App\Interfaces\Castable;

enum RequestParamsEnum: string implements Castable
{
    use EnumActions;
    use EnumCastParams;

    /**
     * Swarm-API Documentation
     * APi is case sensitive, don't change cases.
     */

    case Language = "language";
    case SiteId = "site_id";
    case SwarmSession = "swarm-session";

    case Username = "username";
    case Password = "password";
    case GoogleRecaptchaResponse = "g_recaptcha_response";


    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed
    {
        /**
         * Default cast is string,
         * so only register non string cases
         */
        $castEnum = match ($this) {

            self::SiteId => CastEnum::Int,

            default => CastEnum::String
        };

        return $castEnum->cast($value);
    }
}
