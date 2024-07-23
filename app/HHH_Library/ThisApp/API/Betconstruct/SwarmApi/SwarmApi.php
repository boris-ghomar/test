<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi;

use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\GetUserRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\LoginRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\SessionRequest;

class SwarmApi
{

    /************** Api Requests **************/

    /**
     * Request session
     * For all command needs to request session
     *
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\SessionRequest
     */
    public static function requestSession(): SessionRequest
    {
        return (new SessionRequest())->send();
    }

    /**
     * Login client
     * For all command needs to request session
     *
     * @param  string $username
     * @param  string $password
     * @param  ?string $googleRecaptchaResponse
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\LoginRequest
     */
    public static function login(string $username, string $password, ?string $googleRecaptchaResponse): LoginRequest
    {
        return (new LoginRequest($username, $password, $googleRecaptchaResponse))->send();
    }

    /**
     * Login client
     * For all command needs to request session
     *
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\GetUserRequest
     */
    public static function getUser(): GetUserRequest
    {
        return (new GetUserRequest())->send();
    }


    /************** Api Requests END **************/
}
