<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi;

use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\JsonHelper;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ApiConfigEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ErrorEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SwarmApiController extends Controller
{

    /**
     * Get initial data for javascript api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInitialData(): JsonResponse
    {
        $data = [
            'webSocketUrl' => ApiConfigEnum::WebSocketUrl->getValue(),
            'WebSocketUrlAlternative' => ApiConfigEnum::WebSocketUrlAlternative->getValue(),
            'siteId' => ApiConfigEnum::SiteId->getValue(),
            'language' => LocaleEnum::getSessionLocale()->isoCode3Dig(),
        ];

        return JsonResponseHelper::successResponse($data, 'Success');
    }

    /**
     * Register log message
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerLog(Request $request): JsonResponse
    {
        $type = $request->input('type');
        $message = $request->input('message');
        $clientIP = $request->input('clientIP');
        $webSocketUrl = $request->input('webSocketUrl');

        if (!empty($message)) {

            $message = sprintf(
                "Client IP: %s\nWebsocket URL: %s\n%s",
                $clientIP,
                $webSocketUrl,
                $message,
            );

            LogCreator::createLog(__CLASS__, __FUNCTION__, $type, $message, 'Swarm Websocket');
        }

        $data = [];
        return JsonResponseHelper::successResponse($data);
    }

    /**
     * Get error message
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getErrorMessage(Request $request): JsonResponse
    {
        $message = null;
        $commmandJson = $request->input('commmand');
        $dataJson = $request->input('data');
        $clientIP = $request->input('clientIP');
        $webSocketUrl = $request->input('webSocketUrl');

        if (JsonHelper::isJson($dataJson)) {

            $data = json_decode($dataJson, true);

            if (isset($data['code'])) {

                $errorMsg = isset($data['msg']) ? $data['msg'] : $dataJson;

                if (!empty($errorMsg))
                    $message = ErrorEnum::findMessage($data['code'], $errorMsg);
            }
        }


        if (is_null($message)) {
            // Partner unknown error

            $connectionCases = [
                '{"isTrusted":true}',
                '{"isTrusted":false}',
            ];

            $ignoreCases = $connectionCases;

            if (!in_array($dataJson, $ignoreCases)) {

                $message = sprintf(
                    "Error not found.\nClient IP: %s\nWebsocket URL: %s\nCommand: %s\nPartner response: %s",
                    $clientIP,
                    $webSocketUrl,
                    $commmandJson,
                    $dataJson
                );

                LogCreator::createLogError(
                    __CLASS__,
                    __FUNCTION__,
                    $message,
                    ApiConfigEnum::ApiName->getValue()
                );
            }

            if (in_array($dataJson, $connectionCases)) {
                // "isTrusted" error created due to the rejection of the connection websocket by Betconstruct.
                $message = __('bc_api.websocketConnectionError');
            } else
                $message = __('bc_api.UnknownPartnerError');
        }

        $data = [
            'message' =>  $message,
        ];

        return JsonResponseHelper::successResponse($data, 'Success');
    }
}
