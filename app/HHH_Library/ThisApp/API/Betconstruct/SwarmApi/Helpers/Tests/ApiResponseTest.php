<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Tests;

use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\GetUserRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\LoginRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\SessionRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Tests\Enums\TestResponseEnum;
use Illuminate\Http\JsonResponse;

class ApiResponseTest
{

    /**
     * makeResponse
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Tests\Enums\TestResponseEnum $response
     * @return \Illuminate\Http\JsonResponse
     */
    private static function makeResponse(TestResponseEnum $response): JsonResponse
    {
        $responseArray = json_decode($response->value, true);
        return JsonResponseHelper::successResponse($responseArray);
    }

    /**
     * Request session
     * For all command needs to request session
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\SessionRequest
     */
    public static function requestSession(TestResponseEnum|null $response = null): SessionRequest
    {
        $response = is_null($response) ? TestResponseEnum::RequestSession_Success : $response;
        return (new SessionRequest())->testResponse(self::makeResponse($response));
    }

    /**
     * Login client
     * Needs to request session
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\LoginRequest
     */
    public static function login(TestResponseEnum|null $response = null): LoginRequest
    {
        $response = is_null($response) ? TestResponseEnum::Login_Success : $response;
        return (new LoginRequest('username', 'password', 'googleRecaptchaResponse'))->testResponse(self::makeResponse($response));
    }

    /**
     * Request session
     * For all command needs to request session
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\GetUserRequest
     */
    public static function getUser(TestResponseEnum|null $response = null): GetUserRequest
    {
        $response = is_null($response) ? TestResponseEnum::GetUser_Success : $response;
        return (new GetUserRequest())->testResponse(self::makeResponse($response));
    }
}
