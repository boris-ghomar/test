<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests;

use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet\FilterBetModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\SuperApiRequest;

class GetBetsRequest extends SuperApiRequest
{

    /**
     * Based on Betconstruct ExternalAdmin-API Documentation
     *
     * Get bets by filter.
     *
     * Note:
     * 1. This request must have at least 1 filter.
     * 2. Do not send big requests.
     *
     */

    /*
        Sample Command

        CUrl:

        $url= "http://agp-externaladmin.betconstruct.com/api/en/934/Report/GetBets";
        $body = "{"Id":222164184,"RequestHash":"4845a9a8d073c2ed48bcb54063d957d3a2601e5c631b422bbba1b458fa99bd79"}";
        $headers = ["Content-Type" => "application/json"];
     */

    /*
        Sample API Server Response

        {
            "StatusCode":"0",
            "Data":
                [
                    {
                        "AuthToken":null,
                        "TransactionId":147906587545,
                        "BetId":3778725021,
                        "HashCode":"1695133",
                        "Amount":5.00,
                        "Created":"2024-01-17T14:08:44.521Z",
                        "CalcDate":null,
                        "BetType":1,
                        "SystemMinCount":null,
                        "SelectionCount":1,
                        "TotalPrice":1.220,
                        "BonusBetAmount":null,
                        "BonusId":null,
                        "Source":42,
                        "State":1,
                        "CashoutAmount":null,
                        "AcceptTypeId":0,
                        "OddType":0,
                        "Selections":
                            [
                                {
                                    "SelectionId":4296219950,
                                    "SelectionName":"W2",
                                    "MarketTypeId":5498,
                                    "MarketName":"Match Result",
                                    "MatchId":23729087,
                                    "MatchShortId":122219,
                                    "MatchName":"Iraq - Japan",
                                    "MatchStartDate":"2024-01-19T11:30:00Z",
                                    "RegionId":3,
                                    "RegionName":"Asia",
                                    "CompetitionId":1691,
                                    "CompetitionName":"AFC Asian Cup",
                                    "SportId":1,
                                    "SportName":"Football",
                                    "SportAlias":"Soccer",
                                    "Price":1.220,
                                    "IsLive":false,
                                    "Basis":0.00,
                                    "MatchInfo":null,
                                    "IsOutright":false,
                                    "State":0,
                                    "SelectionScore":"",
                                    "ReSettlementReason":null,
                                    "RequestHash":null
                                }
                            ],
                        "IsLive":false,
                        "WinAmount":0.00,
                        "Currency":"IRT",
                        "PaymentDate":null,
                        "BetShop":null,
                        "CashDesk":null,
                        "CashDeskId":null,
                        "InfoCashDeskId":null,
                        "ClientId":101452623,
                        "Number":null,
                        "ExternalId":null,
                        "PaymentBetShop":null,
                        "BarCode":37787250212,
                        "BetShopGroupName":"",
                        "PossibleTaxAmount":0.00,
                        "ParentBetId":null,
                        "PossibleWin":6.10,
                        "AcceptLowerOdds":false,
                        "IsEachWay":null,
                        "HashToCheck":null,
                        "RequestHash":null
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
     * Get Client information
     *
     * Sample:
     * $filters = [
     *       FilterClientModelEnum::Id->filter(62298070),
     *       FilterClientModelEnum::IsLocked->filter(false)
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

        if (count($requiredParams) < 1) {

            $this->setResponse(JsonResponseHelper::errorResponse(
                null,
                'This request must have at least 1 filter.',
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
        return 'Report/GetBets';
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
