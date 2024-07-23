<?php

namespace App\HHH_Library\ThisApp\API\JustCall\Helpers\Tests;

use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\JustCall\Helpers\Requests\SendTextRequest;
use App\HHH_Library\ThisApp\API\JustCall\Helpers\Tests\Enums\TestResponseEnum;
use Illuminate\Http\JsonResponse;

class ApiResponseTest
{

    /**
     * makeResponse
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum $response
     * @return \Illuminate\Http\JsonResponse
     */
    private static function makeResponse(TestResponseEnum $response): JsonResponse
    {
        $responseArray = json_decode($response->value, true);
        return JsonResponseHelper::successResponse($responseArray);
    }

    /**
     * Send SMS message
     *
     * @param  string $to
     * @param  string $body
     * @param null|\App\HHH_Library\ThisApp\API\JustCall\Helpers\Tests\Enums\TestResponseEnum $response
     * @return \App\HHH_Library\ThisApp\API\JustCall\Helpers\Requests\SendTextRequest
     */
    public static function sendText(string $to, string $body, TestResponseEnum|null $response = null): SendTextRequest
    {
        $response = is_null($response) ? TestResponseEnum::SendText_Success : $response;
        return (new SendTextRequest($to, $body))->testResponse(self::makeResponse($response));
    }
}
