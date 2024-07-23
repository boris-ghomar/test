<?php

namespace App\HHH_Library\general\php;

use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use Illuminate\Http\JsonResponse;

class JsonResponseHelper
{

    /**
     * This function is used to declare a successful result.
     *
     * @param mixed $data
     * @param  string|array|null $message
     * @param int $code HTTP Response code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function successResponse(mixed $data, string|array|null $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status'    => ApiStatusEnum::Success->name,
            'message'   => empty($message) ? HttpResponseStatusCode::getMessageByCode($code) : $message,
            'data'      => $data
        ], $code);
    }

    /**
     * This function is used to declare the result unsuccessful.
     * To handle the error by the application and the user,
     * the message in the language of the user and
     * the text of the error must be sent in English.
     *
     * For more transparency you can use
     * the key used in translating the message.
     *
     * Example::
     *          $error = 'auth.custom.failed';
     *          $message = trans($error);
     *
     *
     * @param  ?string $error : Must be English
     * @param  string|array|null $message
     * @param int $code
     * @param array|string|null $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function errorResponse(?string $error, string|array|null $message, int $code = 500, array|string|null $data = null): JsonResponse
    {
        return response()->json([
            'status'    => ApiStatusEnum::Failed->name,
            'error'     => empty($error) ?  HttpResponseStatusCode::getMessageByCode($code) : $error,
            'message'   => empty($message) ?  HttpResponseStatusCode::getMessageByCode($code) : $message,
            'data'      => $data,
        ], $code);
    }

    /**
     * This function checks whether the response is successful or not.
     *
     * @param \Illuminate\Http\JsonResponse|null $jsonResponse
     * @return bool
     */
    public static function isJsonResponseSuccess(JsonResponse|null $jsonResponse): bool
    {
        $responseData = self::unpackJsonResponse($jsonResponse);

        if (!is_null($responseData)) {

            if (isset($responseData->status) &&  $responseData->status === ApiStatusEnum::Success->name)
                return true;
        }

        return false;
    }

    /**
     * Get error from json response
     *
     * @param \Illuminate\Http\JsonResponse|null $jsonResponse
     * @return ?string
     */
    public static function getJsonResponseError(JsonResponse|null $jsonResponse): ?string
    {

        $responseData = self::unpackJsonResponse($jsonResponse);

        if (!is_null($responseData)) {

            if (isset($responseData->error))
                return $responseData->error;
        }

        return null;
    }

    /**
     * Get message from json response
     *
     * @param \Illuminate\Http\JsonResponse|null $jsonResponse
     * @return ?string
     */
    public static function getJsonResponseMessage(JsonResponse|null $jsonResponse): ?string
    {

        $responseData = self::unpackJsonResponse($jsonResponse);

        if (!is_null($responseData)) {

            if (isset($responseData->message))
                return $responseData->message;
        }

        return null;
    }

    /**
     * Get data from json response
     *
     * @param \Illuminate\Http\JsonResponse|null $jsonResponse
     * @return ?string
     */
    public static function getJsonResponseData(JsonResponse|null $jsonResponse): ?string
    {

        $responseData = self::unpackJsonResponse($jsonResponse);

        if (!is_null($responseData)) {

            if (isset($responseData->data)) {

                $data = $responseData->data;
                return JsonHelper::isJson($data) ? $data : json_encode($data);
            }
        }

        return null;
    }

    /**
     * Unpack json response.
     *
     * @param \Illuminate\Http\JsonResponse|null $jsonResponse
     * @return mixed
     */
    private static function unpackJsonResponse(JsonResponse|null $jsonResponse): mixed
    {

        try {
            if (!is_null($jsonResponse)) {

                return $jsonResponse->getData();
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        return null;
    }
}
