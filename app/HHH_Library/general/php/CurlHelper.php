<?php

namespace App\HHH_Library\general\php;

use App\HHH_Library\general\php\Enums\ErrorEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * NOTICE:
 *
 * resource: https://www.php.net/manual/en/ref.curl.php
 *
 */

class CurlHelper
{

    private $curl;

    private string $requestMethod = HttpMethodEnum::GET->name;

    private ?string $url = null;
    private ?string $userAgent = null; // Exp: 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';

    private bool $followLocation = true;
    private bool $returnStatusCode = true;
    private bool $checkSSLcertificates = false;

    private int $connectTimeout = 10;
    private int $requestTimeout = 50;

    private array $headers = ['Content-Type' => 'application/json'];

    private array|string|null $postFields = null;

    public function __construct(string $url)
    {
        $this->setUrl($url);
    }

    /**
     * init the cURL
     *
     * @return JsonResponse
     */
    protected function init(): JsonResponse
    {

        // Create a new cURL resource
        $curl = curl_init();

        if (!$curl) {

            return JsonResponseHelper::errorResponse(
                null,
                "cURL init error: Couldn't initialize a cURL handle",
                HttpResponseStatusCode::InternalServerError->value
            );
        }

        // Set the file URL to fetch through cURL
        if (!is_null($this->getUrl()))
            curl_setopt($curl, CURLOPT_URL, $this->getUrl());
        else
            return JsonResponseHelper::errorResponse(
                null,
                "cURL init error: The Curl request needs url.",
                HttpResponseStatusCode::UnprocessableEntity->value
            );

        // Set a different user agent string (Googlebot)
        if (!is_null($this->getUserAgent()))
            curl_setopt($curl, CURLOPT_USERAGENT, $this->getUserAgent());

        // Follow redirects, if any
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $this->getFollowLocation());

        // Fail the cURL request if response code = 400 (like 404 errors)
        curl_setopt($curl, CURLOPT_FAILONERROR, 0);

        // Returns the status code
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, $this->getReturnStatusCode());

        // Wait 10 seconds to connect and set 0 to wait indefinitely
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->getConnectTimeout());

        // Execute the cURL request for a maximum of 50 seconds
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getRequestTimeout());

        // Do not check the SSL certificates
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $this->getCheckSSLcertificates());
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->getCheckSSLcertificates());

        // Add custom headers
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders());


        // "POST", "PUT", "DELETE" Methods
        if (
            $this->getRequestMethod() == HttpMethodEnum::POST->name
            || $this->getRequestMethod() == HttpMethodEnum::PUT->name
            || $this->getRequestMethod() == HttpMethodEnum::DELETE->name
        ) {

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->getRequestMethod());

            $postFields = $this->getPostFields();

            if (!is_null($postFields)) {

                if (is_array($postFields)) {

                    curl_setopt(
                        $curl,
                        CURLOPT_POSTFIELDS,
                        http_build_query($this->getPostFields())
                    );
                } else
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $this->getPostFields());
            }
        }

        $this->curl = $curl;

        return JsonResponseHelper::successResponse(null, 'The initial preparation was done successfully');
    }

    /**
     * execute the cURL request.
     *
     * @return JsonResponse
     */
    public function exec(): JsonResponse
    {

        $initRes = $this->init();

        if (JsonResponseHelper::isJsonResponseSuccess($initRes)) {

            $curl = $this->curl;

            // Fetch the URL and save the content in $html variable
            $content = curl_exec($curl);

            // Check if any error has occurred. curl_errno: Return the last error number
            $curlErrorNumber = curl_errno($curl);
            if ($curlErrorNumber) {

                $error = sprintf("%s_%s", ErrorEnum::CurlError->name, $curlErrorNumber);
                $curlError = curl_error($curl);
                $message = ErrorEnum::hasName($error) ? ErrorEnum::findMessage($error, $curlError) : sprintf('%s: %s', ErrorEnum::CurlError->value, $curlError);

                $response =  JsonResponseHelper::errorResponse(
                    $error,
                    $message,
                    HttpResponseStatusCode::ExpectationFailed->value
                );
            } else {
                // Return cURL returned result
                $response = JsonResponseHelper::successResponse($content);
            }

            // close cURL resource to free up system resources
            curl_close($curl);
            return $response;
        } else
            return $initRes;
    }

    /******** Setter & Getter ********/


    /**
     * Set request url
     * Also you can set the file URL to fetch through cURL
     *
     * @param  string $url
     * @return void
     */
    public function setUrl(string $url): void
    {
        $this->url = Str::of($url)->trim()->isEmpty() ? null : $url;
    }

    /**
     * get Url
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set a different user agent string (Googlebot)
     *
     * @param  ?string $userAgent
     * @return void
     */
    public function setUserAgent(?string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    /**
     * get user-Agent
     *
     * @return ?string
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * set follow location
     * Follow redirects, if any
     *
     * @param  bool $followLocation
     * @return void
     */
    public function setFollowLocation(bool $followLocation): void
    {
        $this->followLocation = $followLocation;
    }

    /**
     * get FollowLocation
     *
     * @return bool
     */
    public function getFollowLocation(): bool
    {
        return $this->followLocation;
    }

    /**
     * Set Returns the status code
     *
     * @param  bool $returnStatusCode
     * @return void
     */
    public function setReturnStatusCode(bool $returnStatusCode): void
    {
        $this->returnStatusCode = $returnStatusCode;
    }

    /**
     * get ReturnStatusCode
     *
     * @return bool
     */
    public function getReturnStatusCode(): bool
    {
        return $this->returnStatusCode;
    }

    /**
     * setCheckSSLcertificates
     * true: check the SSL certificates
     * false: Do not check the SSL certificates
     *
     * @param  bool $checkSSLcertificates
     * @return void
     */
    public function setCheckSSLcertificates(bool $checkSSLcertificates): void
    {
        $this->checkSSLcertificates = $checkSSLcertificates;
    }

    /**
     * getCheckSSLcertificates
     *
     * true: check the SSL certificates
     * false: Do not check the SSL certificates
     *
     * @return bool
     */
    public function getCheckSSLcertificates(): bool
    {
        return $this->checkSSLcertificates;
    }

    /**
     * setConnectTimeout
     * set seconds to wait for connection
     *
     * @param  int $connectTimeout
     * @return void
     */
    public function setConnectTimeout(int $connectTimeout): void
    {
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * getConnectTimeout
     * get seconds to wait for connection
     *
     * @return int
     */
    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    /**
     * setRequestTimeout
     * Execute the cURL request for a maximum of $requestTimeout seconds
     *
     * @param  int $requestTimeout
     * @return void
     */
    public function setRequestTimeout(int $requestTimeout): void
    {
        $this->requestTimeout = $requestTimeout;
    }

    /**
     * getRequestTimeout
     *
     * @return int
     */
    public function getRequestTimeout(): int
    {
        return $this->requestTimeout;
    }

    /**
     * Add an item to request headers
     *
     * @param  string $key
     * @param  ?string $value
     * @return void
     */
    public function setHeader(string $key, ?string $value): void
    {
        $key = Str::of($key)->trim()->lower();
        $value = Str::of($value)->trim()->lower();

        if (!Str::of($key)->isEmpty() && !Str::of($value)->isEmpty()) {

            $this->headers[$key->toString()] = $value->toString();
        }
    }

    /**
     * Add multiple headers
     *
     * @param  array $headers
     * @return void
     */
    public function setHeaders(array $headers): void
    {
        foreach ($headers as $key => $value)
            $this->setHeader($key, $value);
    }

    /**
     * Get request headers as $key => $value array
     *
     * @return array
     */
    public function getHeadersItems(): array
    {
        return $this->headers;
    }

    /**
     * Get request headers in request header type
     *
     * @return array
     */
    public function getHeaders(): array
    {
        $headers = [];
        foreach ($this->getHeadersItems() as $key => $value) {

            $item = sprintf('%s: %s', $key, $value);

            if (!in_array($item, $headers))
                array_push($headers, sprintf('%s: %s', $key, $value));
        }

        return $headers;
    }

    /**
     * Set request method
     *
     * @param  string $method
     * @return void
     */
    public function setRequestMethod(string $method): void
    {

        $method = Str::of($method)->trim()->upper()->toString();

        if (!Str::of($method)->isEmpty() && in_array($method, HttpMethodEnum::names())) {
            $this->requestMethod = $method;
        }
    }

    /**
     * Get request method
     *
     * @return string
     */
    public function getRequestMethod(): string
    {

        return $this->requestMethod;
    }

    /**
     * Set "Post Fields" for POST request
     *
     * @param  array|string|null $postFields
     * @return void
     */
    public function setPostFields(array|string|null $postFields): void
    {
        $this->postFields = $postFields;
    }

    /**
     * Get adjusted PostFields
     *
     * @return array|string|null
     */
    public function getPostFields(): array|string|null
    {
        return $this->postFields;
    }
    /******** Setter & Getter END ********/
}
