<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests;

use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ApiConfigEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\RequestParamsEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\SessionEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\SuperApiRequest;

class SessionRequest extends SuperApiRequest
{
    /**
     * Based on Betconstruct Swarm-API Documentation
     */

    /*
        Sample Command

        {
            "command": "request_session",
            "params": {
                "site_id": 1,
                "language": "arm",

                // optional
                "source": 1, // source field
                "terminal": 123 // terminal field
                "afec": "Art3sd3dsAD21Bn..." // Device fingerprint
            }
        }
    */

    /*
        Sample API Server Response

        {
            "code": 0,
            "data": {
                "ctx": {
                    "k_type": "None",
                    "site": 934,
                    "tree_mode": "dict"
                    },
                "host": "europe-west4-a-c7-swarm-test-63lg",
                "ip": "91.72.74.74, 162.158.56.156, 34.120.174.72",
                "recaptcha_actions": [
                    "login",
                    "register",
                    "apply_promo_codes",
                    "do_bet",
                    "get_max_bet",
                    "apply_scratch_card"
                ],
                "recaptcha_enabled": true,
                "recaptcha_version": 3,
                "sid": "9b296d36-31d9-4792-4d98-90c4123ee9e0-1",
                "site_key": "6LeR4MAUAAAAAEsIF4FFxLHl20dZpu_8_K9osriA",
                "version": "1.0.81"
            }
        }
    */

    // Result params
    const Data = "data";
    const Ctx = "ctx";
    const KType = "k_type";
    const Site = "site";
    const TreeMode = "tree_mode";
    const Host = "host";
    const IP = "ip";
    const RecaptchaActions = "recaptcha_actions";
    const RecaptchaEnabled = "recaptcha_enabled";
    const RecaptchaVersion = "recaptcha_version";
    const SessionId = "sid";
    const SiteKey = "site_key";
    const Version = "version";


    /*************************** implements ***************************/

    /**
     * parent abstract
     * Get command name
     *
     * @return string
     */
    public function command(): string
    {
        return 'request_session';
    }

    /**
     * parent abstract
     * Get request required headers
     *
     * @return array
     */
    public function requiredHeaders(): array
    {
        return [];
    }

    /**
     * parent abstract
     * Get request required params
     *
     * @return array
     */
    public function requiredParams(): array
    {
        $language = LocaleEnum::getSessionLocale()->isoCode3Dig();

        return [
            RequestParamsEnum::SiteId->value => ApiConfigEnum::SiteId->getValue(),
            RequestParamsEnum::Language->value => is_null($language) ? ApiConfigEnum::DefaultRequestLanguage->getValue() : $language,
        ];
    }

    /**
     * parent abstract
     * Handle returned response from api server
     *
     * @return self
     */
    public function handleResponse(): self
    {
        if ($this->getStatus()->name === ApiStatusEnum::Success->name)
            $this->onSuccess();

        return $this;
    }

    /*************************** implements END ***************************/

    /**
     * Handle success response
     *
     * @return void
     */
    private function onSuccess()
    {
        // put repatcha site key in session
        if ($this->getRecaptchaEnabled())
            SessionEnum::GoogleRecaptchSiteKey->setSession($this->getSiteKey());

        SessionEnum::SwarmSessionId->setSession($this->getSesseionId());
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
     * Get Ctx
     *
     * @return ?array
     */
    public function getCtx(): ?array
    {
        return $this->getParamValue($this->getData(), self::Ctx, true);
    }

    /**
     * Get KType
     *
     * @return ?string
     */
    public function getKType(): ?string
    {
        return $this->getParamValue($this->getCtx(), self::KType);
    }

    /**
     * Get site
     *
     * @return int|null
     */
    public function getSite(): int|null
    {
        return $this->getParamValue($this->getCtx(), self::Site);
    }

    /**
     * Get TreeMode
     *
     * @return ?string
     */
    public function getTreeMode(): ?string
    {
        return $this->getParamValue($this->getCtx(), self::TreeMode);
    }

    /**
     * Get Host
     *
     * @return ?string
     */
    public function getHost(): ?string
    {
        return $this->getParamValue($this->getData(), self::Host);
    }

    /**
     * Get IP
     *
     * @param bool $asArray $asArray ? output will be return as array : string
     * @return string|array|null
     */
    public function getIP(bool $asArray = false): string|array|null
    {
        $ip = $this->getParamValue($this->getData(), self::IP);

        if (!is_null($ip)) {
            return array_map('trim', explode(',', $ip));
        }

        return $ip;
    }

    /**
     * Get Recaptcha Actions
     *
     * @return ?array
     */
    public function getRecaptchaActions(): ?array
    {
        return $this->getParamValue($this->getData(), self::RecaptchaActions, true);
    }

    /**
     * Get Recaptcha Enabled
     *
     * @return bool|null
     */
    public function getRecaptchaEnabled(): bool|null
    {
        return $this->getParamValue($this->getData(), self::RecaptchaEnabled);
    }

    /**
     * Get Recaptcha Version
     *
     * @return int|null
     */
    public function getRecaptchaVersion(): int|null
    {
        return $this->getParamValue($this->getData(), self::RecaptchaVersion);
    }

    /**
     * Get Sesseion Id
     *
     * @return ?string
     */
    public function getSesseionId(): ?string
    {
        return $this->getParamValue($this->getData(), self::SessionId);
    }

    /**
     * Get Site Key
     *
     * @return ?string
     */
    public function getSiteKey(): ?string
    {
        return $this->getParamValue($this->getData(), self::SiteKey);
    }

    /**
     * Get Version
     *
     * @return ?string
     */
    public function getVersion(): ?string
    {
        return $this->getParamValue($this->getData(), self::Version);
    }


    /*************************** request getter functions  END ***************************/
}
