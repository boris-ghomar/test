<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests;

use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\RequestParamsEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\SessionEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\SuperApiRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\SwarmApi;

class LoginRequest extends SuperApiRequest
{

    /**
     * Based on Betconstruct Swarm-API Documentation
     */

    /*
        Sample Command

        {
            "command": "login",
            "params": {
                "username": "testuser",
                "password": "hispassword"
                "g_recaptcha_response": "g_recaptcha_response" ? (if request)
            }
        }
     */

    /*
        Sample API Server Response

        {
            "code": 0,
            "data": {
                "auth_token": "8FDDC059AD2ADED6451A3CDA3727E40C",
                "authentication_status": 0,
                "deposit_count": 8,
                "is_new_client": null,
                "qr_code_origin": null,
                "user_id": 222164184
            }
        }
    */

    // Result params
    const Data = "data";
    const AuthToken = "auth_token";
    const AuthenticationStatus = "authentication_status";
    const DepositCount = "deposit_count";
    const IsNewClient = "is_new_client";
    const QrCodeOrigin = "qr_code_origin";
    const UserId = "user_id";


    private $headers = [];
    private $params = [];

    /**
     * Constructor
     *
     * @param  string $username
     * @param  string $password
     * @param  ?string $googleRecaptchaResponse
     * @return void
     */
    function __construct(string $username, string $password, ?string $googleRecaptchaResponse)
    {
        $this->params[RequestParamsEnum::Username->value] = $username;
        $this->params[RequestParamsEnum::Password->value] = $password;
        if (!is_null($googleRecaptchaResponse))
            $this->params[RequestParamsEnum::GoogleRecaptchaResponse->value] = $googleRecaptchaResponse;

        parent::__construct();
    }

    /*************************** implements ***************************/

    /**
     * parent abstract
     * Get command name
     *
     * @return string
     */
    public function command(): string
    {
        return 'login';
    }

    /**
     * parent abstract
     * Get request required headers
     *
     * @return array
     */
    public function requiredHeaders(): array
    {
        return $this->headers;
    }

    /**
     * parent abstract
     * Get request required params
     *
     * @return array
     */
    public function requiredParams(): array
    {
        return $this->params;
    }

    /**
     * Handle returned response from api server
     *
     * @return self
     */
    public function handleResponse(): self
    {
        return $this;
    }
    /*************************** implements END ***************************/

    /**
     * @override
     * Send request to server
     *
     * @return self
     */
    public function send(): self
    {
        $swarmSessionId = SessionEnum::SwarmSessionId->getSession();

        if (is_null($swarmSessionId)) {

            /** @var SessionRequest $session */
            $session = SwarmApi::requestSession();

            if ($session->getStatus()->name === ApiStatusEnum::Success->name) {
                $this->headers[RequestParamsEnum::SwarmSession->value] = $session->getSesseionId();
            } else {
                $this->setResponse($session->getResponse());
                return $this;
            }
        } else {

            $this->headers[RequestParamsEnum::SwarmSession->value] = $swarmSessionId;
        }

        return parent::send();
    }

    /*************************** request getter functions  ***************************/


    /**
     * Get data
     *
     * @return ?array
     */
    public function getData(): ?array
    {
        return $this->getParamValue($this->getResponseData(), self::Data, true);
    }

    /**
     * Get AuthToken
     *
     * @return ?string
     */
    public function getAuthToken(): ?string
    {
        return $this->getParamValue($this->getData(), self::AuthToken);
    }

    /**
     * Get AuthenticationStatus
     *
     * @return int|null
     */
    public function getAuthenticationStatus(): int|null
    {
        return $this->getParamValue($this->getData(), self::AuthenticationStatus);
    }

    /**
     * Get DepositCount
     *
     * @return int|null
     */
    public function getDepositCount(): int|null
    {
        return $this->getParamValue($this->getData(), self::DepositCount);
    }

    /**
     * Get IsNewClient
     *
     * @return bool|null
     */
    public function getIsNewClient(): bool|null
    {
        return $this->getParamValue($this->getData(), self::IsNewClient);
    }

    /**
     * Get QrCodeOrigin
     *
     * @return ?string
     */
    public function getQrCodeOrigin(): ?string
    {
        return $this->getParamValue($this->getData(), self::QrCodeOrigin);
    }

    /**
     * Get UserId
     *
     * @return int|null
     */
    public function getUserId(): int|null
    {
        return $this->getParamValue($this->getData(), self::UserId);
    }
    /*************************** request getter functions  END ***************************/
}
