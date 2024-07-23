<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests;

use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet\FilterBetModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bonus\FilterPartnerBonusModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\SuperApiRequest;

class GetPartnerBonusesRequest extends SuperApiRequest
{

    /**
     * Based on Betconstruct ExternalAdmin-API Documentation
     *
     * Get bonuses by filter.
     *
     * Note:
     * 1. This request must have at least 1 filter.
     * 2. Do not send big requests.
     *
     */

    /*
        Sample Command

        CUrl:

        $url= "http://agp-externaladmin.betconstruct.com/api/en/934/Bonus/GetBonuses";
        $body = "{"Type":1,"RequestHash":"4845a9a8d073c2ed48bcb54063d957d3a2601e5c631b422bbba1b458fa99bd79"}";
        $headers = ["Content-Type" => "application/json"];
     */

    /*
        Sample API Server Response

        {
	        "StatusCode":"0",
	        "Data":
		        [
			        {
				        "Id":76722,
				        "Name":"Exact Losing Express 4- 10%",
				        "Description":"Bonus on Exact Losing",
				        "InternalDesc":null,
				        "StartDateTS":-62135596800,
				        "EndDateTS":null,
				        "ExpirationDays":null,
				        "Type":1,
				        "Triggertype":0,
				        "ExternalId":null,
				        "IsDisabled":false,
				        "PlayerMaxCount":null,
				        "Note":null,
				        "RequestHash":null
			        },
			        {
				        "Id":76723,
				        "Name":"Exact Losing Express 5- 20%",
				        "Description":"Bonus on Exact Losing",
				        "InternalDesc":null,
				        "StartDateTS":-62135596800,
				        "EndDateTS":null,
				        "ExpirationDays":null,
				        "Type":1,
				        "Triggertype":0,
				        "ExternalId":null,
				        "IsDisabled":false,
				        "PlayerMaxCount":null,
				        "Note":null,
				        "RequestHash":null
			        },
		        ]
        }
    */

    // Result params
    const Data = "Data";


    private $headers = [];
    private $params = [];

    /**
     * Constructor
     * Get Client information
     *
     * Sample:
     * $filters = [
     *       FilterPartnerBonusModelEnum::Id->filter(BonusTypeEnum::SportBonus->value),
     * ];
     *
     *
     * @param  array $filters
     */
    function __construct(array $filters = [])
    {
        $this->params = $this->mergeFilters($filters);
    }

    /**
     * Merge filters
     *
     * @param  array $filters
     * @return array
     */
    private function mergeFilters(array $filters): array
    {
        $res = [];
        foreach ($filters as $filter) {

            $res = array_merge($res, $filter);
        }

        return $res;
    }

    /**
     * @override
     * Send request to server
     *
     * @return self
     */
    public function send(): self
    {
        $this->startTimer();

        $requiredParams = $this->requiredParams();

        if (!isset($requiredParams[FilterPartnerBonusModelEnum::Type->name])) {

            $this->setResponse(JsonResponseHelper::errorResponse(
                null,
                'The type of bonus is not specified.',
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
        return 'Bonus/GetBonuses';
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
        return FilterBetModelEnum::castParams($this->params);
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
