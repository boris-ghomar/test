<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers;

use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\general\php\HashHmacHelper;
use App\HHH_Library\general\php\JsonHelper;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ApiConfigEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ErrorEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\RequestParamsEnum;
use Illuminate\Http\JsonResponse;

abstract class SuperApiRequest
{
    const ResponseCode = "StatusCode";
    const ResponseErrorMessage = "Data";

    const MAX_ATTEMPTS = 3;
    const SLEEP_TIME = 2; // Seconds

    private $response = null; // Api Server Response
    private int $attempts = 1;
    private $beginTime = 0;
    /*************************** implements ***************************/

    /**
     * Get endPoint
     *
     * @return string
     */
    abstract function endPoint(): string;


    /**
     * Get request required headers
     *
     * @return array
     */
    abstract function requiredHeaders(): array;

    /**
     * Get request required params
     *
     * @return array
     */
    abstract function requiredParams(): array;

    /**
     * Handle returned response from api server
     *
     * @return self
     */
    abstract function handleResponse(): self;

    /*************************** implements END ***************************/

    /**
     * Default params per request
     *
     * @return array
     */
    public function defaultHeaders(): array
    {
        return ApiConfigEnum::headers();
    }

    /**
     * Default params per request
     *
     * @return array
     */
    public function defaultParams(): array
    {
        return [];
    }

    /**
     * Get request all headers (defaultHeaders & requiredHeaders)
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return array_merge($this->defaultHeaders(), $this->requiredHeaders());
    }

    /**
     * Get request all params (defaultParams & requiredParams)
     *
     * @return array
     */
    public function getParams(): array
    {
        $params = array_merge($this->defaultParams(), $this->requiredParams());

        $requestHash = HashHmacHelper::ComputeHMAC(JsonHelper::jsonEncode_UNESCAPED_UNICODE($params), ApiConfigEnum::HashKey->getValue(), ApiConfigEnum::HashAlgorithm->getValue());
        $params[RequestParamsEnum::RequestHash->name] = $requestHash;

        return $params;
    }

    /**
     * Make request Url
     *
     * @return string
     */
    private function getRequestUrl(): string
    {
        $url = sprintf(
            '%s%s/%s',
            ApiConfigEnum::ApiUrl->getValue(),
            ApiConfigEnum::PartnerId->getValue(),
            $this->endPoint(),
        );

        return $url;
    }

    /**
     * Send request to server
     *
     * @return self
     */
    public function send(): self
    {
        $this->startTimer();

        $requestUrl =  $this->getRequestUrl();
        $params = $this->getParams();

        $requestHandler = new ApiRequestHandeler($requestUrl);
        $requestHandler->setPostFields(json_encode($params));

        $this->setResponse($requestHandler->exec());
        $this->logApiResponse($params);

        return $this->needToRepeat() ? self::send() : $this->handleResponse();
    }

    /**
     * Test response of api
     *
     * @param  \Illuminate\Http\JsonResponse|null $response
     * @return self
     */
    public function testResponse(JsonResponse $response): self
    {
        $this->setResponse($response);
        return $this->needToRepeat() ? self::testResponse($response) : $this->handleResponse();
    }

    /**
     * Get request result status
     *
     * @return \App\HHH_Library\general\php\Enums\ApiStatusEnum
     */
    public function getStatus(): ApiStatusEnum
    {
        if (JsonResponseHelper::isJsonResponseSuccess($this->getResponse())) {

            // Success result code: 0
            return ($this->getResponseCode() === "0") ? ApiStatusEnum::Success : ApiStatusEnum::FailedExternal;
        } else
            return ApiStatusEnum::FailedInternal;
    }

    /**
     * Set API Server Response
     *
     * @param  \Illuminate\Http\JsonResponse|null $response
     * @return void
     */
    public function setResponse(JsonResponse|null $response): void
    {
        $this->response = $response;
    }

    /**
     * Get API server response
     *
     * @return JsonResponse|null
     */
    public function getResponse(): JsonResponse|null
    {
        if (is_null($this->response))
            $this->send();

        return $this->response;
    }

    /**
     * Get response data
     *
     * @param  string $output "json"|"json_pretty"|"array"|"object"
     * @return mixed
     */
    public function getResponseData(string $output = "array"): mixed
    {
        try {
            $response = JsonResponseHelper::getJsonResponseData($this->getResponse());

            if (is_null($response))
                return null;

            $response = JsonHelper::isJson($response) ? $response : json_encode($response);

            return match ($output) {

                'json'          => $response,
                'json_pretty'   => json_encode(json_decode($response), JSON_PRETTY_PRINT),
                'array'         => json_decode($response, true),
                'object'        => json_decode($response, false),

                default => json_decode($response, true) // array
            };
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Super API get response data error!"
            );
        }
        return null;
    }

    /**
     * Start timer
     *
     * @return float
     */
    protected function startTimer(): float
    {
        return $this->beginTime = microtime(true);
    }

    /**
     * Get elapsed time
     *
     * @param  mixed $decimals (optional)
     * @param  mixed $startTime (optional)
     * @return string
     */
    protected function getElapsedTime(int $decimals = 0, float $startTime = 0): string
    {
        $startTime = $startTime > 0 ? $startTime : $this->beginTime;
        $startTime = $startTime > 0 ? $startTime : microtime(true);

        return number_format(microtime(true) - $startTime, $decimals);
    }

    /**
     * Get param value
     *
     * @param  ?array $data
     * @param  ?string $key
     * @param  bool $expectArray (optinal) if you expect to get array
     * @return mixed
     */
    protected function getParamValue(?array $data, ?string $key, bool $expectArray = false): mixed
    {
        if (is_null($data) || empty($key))
            return null;

        if (!array_key_exists($key, $data))
            return null;

        $value = $data[$key];
        if ($expectArray && !is_array($value))
            $value = array($value);

        return $value;
    }

    /**
     * Checks the received response and if there is
     * a need to repeat the request, it allows the repetition.
     *
     * @return bool
     */
    private function needToRepeat(): bool
    {
        $repeat = false;
        $status = $this->getStatus()->name;

        // Check Status
        if ($status === ApiStatusEnum::FailedExternal->name) {

            $errorCode = $this->getResponseCode();
            // $errorMessage = strtolower($this->getErrorMessage()); // maybe need after

            // Add your logic here

        }

        // Check max try
        if ($repeat) {

            if ($this->attempts < self::MAX_ATTEMPTS) {

                $this->attempts++;
                sleep(self::SLEEP_TIME);
            } else
                $repeat = false;
        }

        return $repeat;
    }

    /**
     * Log Result of API request responce
     *
     * @param array $params
     * @return void
     */
    protected function logApiResponse(array $params): void
    {
        $status = $this->getStatus()->name;
        $calledClass = get_called_class();
        $calledClassName = basename($calledClass);

        $title = sprintf(
            "%s: %s [Elapsed Time: %s s]",
            ApiConfigEnum::ApiName->getValue(),
            $calledClassName,
            $this->getElapsedTime(2)
        );

        $params = json_encode($params);

        switch ($status) {

            case ApiStatusEnum::Success->name:

                LogCreator::createLogInfo(
                    $calledClass,
                    __FUNCTION__,
                    sprintf("Params: %s\nResponse:\n%s", $params,  $this->getResponseData('json')),
                    sprintf("%s (Success Request. Attempt: %d)", $title, $this->attempts)
                );
                break;
            case ApiStatusEnum::FailedInternal->name:

                $response = $this->getResponse();
                LogCreator::createLogError(
                    $calledClass,
                    __FUNCTION__,
                    sprintf("Params: %s\nError Message: %s\nResponse:\n%s", $params, JsonResponseHelper::getJsonResponseMessage($response), $response),
                    sprintf("%s (Attempt: %d)", $title, $this->attempts)
                );
                break;

            case ApiStatusEnum::FailedExternal->name:

                LogCreator::createLogError(
                    $calledClass,
                    __FUNCTION__,
                    sprintf("Params: %s\nError Code:%s\nError Message:%s\nResponse:\n%s", $params, $this->getResponseCode(), $this->getErrorMessage(), $this->getResponseData('json')),
                    sprintf("%s (Attempt: %d)", $title, $this->attempts)
                );
                break;

            default:
                // Unknown STATUS

                LogCreator::createLogError(
                    $calledClass,
                    __FUNCTION__,
                    "Unknown status code received.",
                    sprintf("%s (Unknown Status Code: %s)", $title, $status)
                );
                break;
        }
    }

    /*************************** request common getter functions  ***************************/

    /**
     * Get Api response code
     *
     * @return ?string
     */
    public function getResponseCode(): ?string
    {
        return $this->getParamValue($this->getResponseData(), self::ResponseCode);
    }

    /**
     * Get Api server response error message
     * This message is allways english
     *
     * json path: ResponseData->msg
     *
     * @return ?string
     */
    public function getErrorMessage(): ?string
    {
        $status = $this->getStatus()->name;

        if ($status === ApiStatusEnum::FailedInternal->name) {

            return JsonResponseHelper::getJsonResponseMessage($this->getResponse());
        } else if ($status === ApiStatusEnum::FailedExternal->name) {

            // This message is allways english
            $msg = $this->getParamValue($this->getResponseData(), self::ResponseErrorMessage);

            return ErrorEnum::findMessage($this->getResponseCode(), $msg);
        } else
            return __('bc_api.UnknownError');
    }

    /*************************** request common getter functions  END ***************************/
}
