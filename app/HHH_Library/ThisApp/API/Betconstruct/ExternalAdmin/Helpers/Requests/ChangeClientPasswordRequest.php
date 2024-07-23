<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests;

use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\SuperApiRequest;

class ChangeClientPasswordRequest extends SuperApiRequest
{

    /**
     * Based on Betconstruct ExternalAdmin-API Documentation
     *
     * Update client
     *
     * Note:
     * 1. This request must have at least 1 filter.
     * 2. Do not send big requests.
     *
     */

    /*
        Sample Command

        CUrl:

        $url= "http://agp-externaladmin.betconstruct.com/api/en/934/Client/UpdateClient";
        $body = "{"Id":222164184,"RequestHash":"4845a9a8d073c2ed48bcb54063d957d3a2601e5c631b422bbba1b458fa99bd79"}";
        $headers = ["Content-Type" => "application/json"];
     */

    /*
        Sample API Server Response

        {
            "StatusCode": "0",
            "Data": [
                        {
                            "Id": 222164184,
                            "CurrencyId": "TOM",
                            "IBAN": null,
                            "Name": "toman ferhad",
                            "FirstName": "ferhad",
                            "LastName": "toman",
                            "MiddleName": null,
                            "Login": "ferhadtom",
                            "RegionCode": "ZW",
                            "Gender": null,
                            "ProfileId": null,
                            "DocNumber": null,
                            "PersonalId": null,
                            "Address": null,
                            "Email": "ferhadtom@betcartemail.com",
                            "Language": "ar",
                            "Phone": "00989911200505",
                            "MobilePhone": null,
                            "BirthDateStamp": 446515200,
                            "City": null,
                            "PromoCode": null,
                            "BTag": null,
                            "ExtAgentId": null,
                            "TimeZone": null,
                            "IsLocked": false,
                            "IsSubscribedToNewsletter": false,
                            "CreatedStamp": 1588659974,
                            "ModifiedStamp": 1684862488,
                            "ExcludedStamp": null,
                            "RFId": null,
                            "NickName": null,
                            "ResetCode": null,
                            "ResetExpireDateStamp": null,
                            "DocIssuedBy": null,
                            "Balance": 212600.12,
                            "LastLoginIp": "91.72.74.74",
                            "LastLoginTimeStamp": 1684998141,
                            "PreMatchSelectionLimit": 100.00,
                            "LiveSelectionLimit": 100.00,
                            "IsVerified": false,
                            "SportsbookProfileId": 6,
                            "GlobalLiveDelay": null,
                            "ExcludedLastStamp": null,
                            "UnplayedBalance": 155800.12,
                            "IsTest": false,
                            "Password": null,
                            "ExternalId": null,
                            "ZipCode": null,
                            "TermsAndConditionsVersion": null,
                            "CanDeposit": null,
                            "CanWithdraw": null,
                            "CanLogin": null,
                            "CanBet": null,
                            "IsExcludedFromBonuses": false,
                            "CustomPlayerCategory": null,
                            "RequestHash": null
                        }
            ]
        }
    */

    // Result params
    const Data = "Data";


    private $headers = [];
    private $params = [];

    /**
     * Constructor
     *
     * @param  int|string $clientId
     * @param string $username
     * @param string $newPassword
     */
    function __construct(int|string $clientId, string $username, string $newPassword)
    {
        $this->params['ClientId'] = $clientId;
        $this->params[ClientModelEnum::Login->name] = $username;
        $this->params['NewPassword'] = $newPassword;
    }

    /**
     * @override
     * Send request to server
     *
     * @return self
     */
    public function send(): self
    {
        $minRequiredParams = 3;

        $this->startTimer();

        $requiredParams = $this->requiredParams();

        if (count($requiredParams) < $minRequiredParams) {

            $this->setResponse(JsonResponseHelper::errorResponse(
                null,
                'This request must have at least ' . $minRequiredParams . ' data more than ID.',
                HttpResponseStatusCode::BadRequest->value,
            ));
            $this->logApiResponse($requiredParams);

            return $this;
        }

        return parent::send();
    }

    /*************************** implements ***************************/

    /**
     * parent abstract
     * Get endPoint
     *
     * @return string
     */
    public function endPoint(): string
    {
        return 'Client/ChangeClientPassword';
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

    /*************************** request getter functions  ***************************/


    /**
     * Get data
     *
     * @return ?array List<Betconstruct ClientModel>
     */
    public function getData(): ?array
    {
        return $this->getParamValue($this->getResponseData(), self::Data, true);
    }


    /*************************** request getter functions  END ***************************/
}
