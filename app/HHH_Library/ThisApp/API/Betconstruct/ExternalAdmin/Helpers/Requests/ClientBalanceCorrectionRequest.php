<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests;

use App\Enums\General\CurrencyEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Balance\BalanceCorrectionTypeEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Balance\ClientBalanceCorrectionModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\SuperApiRequest;

class ClientBalanceCorrectionRequest extends SuperApiRequest
{

    /**
     * Based on Betconstruct ExternalAdmin-API Documentation
     *
     */

    /*
        Sample Command

        CUrl:

        $url= "http://agp-externaladmin.betconstruct.com/api/en/934/Client/CreateClientBalanceCorrection";
        $body = "{"ClientId":101452623,"CurrencyId":"IRT","Amount":10.01,"DocumentType":301,"Info":"test","RequestHash":"4999b85deff9ff253c32113a36eba6e96b99efe1a221bf459dae7741c5afda21"}";
        $headers = ["Content-Type" => "application/json"];
     */

    /*
        Sample API Server Response

        {
            "StatusCode": "0",
            "Data": {
                        "ClientId": 101452623,
                        "CurrencyId": "IRT",
                        "PaymentSystemId": null,
                        "Amount": 10.01,
                        "Info": "test",
                        "DocumentType": 301,
                        "DocumentId": 151323534990,
                        "ExternalId": null,
                        "RequestHash": null
                    }
        }
    */

    // Result params
    const Data = "Data";


    private $headers = [];
    private $params = [];

    /**
     * Constructor
     *
     * @param  int|string $clientId Partner client ID
     * @param \App\Enums\General\CurrencyEnum $currency
     * @param  float $amount
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Balance\BalanceCorrectionTypeEnum $balanceCorrectionType
     * @param  ?string $info
     */
    function __construct(int|string $clientId, CurrencyEnum $currency,  float $amount, BalanceCorrectionTypeEnum $balanceCorrectionType, ?string $info = null)
    {
        $this->params[ClientBalanceCorrectionModelEnum::ClientId->name] = ClientBalanceCorrectionModelEnum::ClientId->cast($clientId);
        $this->params[ClientBalanceCorrectionModelEnum::CurrencyId->name] = strtoupper($currency->name);
        $this->params[ClientBalanceCorrectionModelEnum::Amount->name] = $this->modifyAmount($amount);
        $this->params[ClientBalanceCorrectionModelEnum::DocumentType->name] = $balanceCorrectionType->value;

        if (!empty($info))
            $this->params[ClientBalanceCorrectionModelEnum::Info->name] = $info;
    }

    /**
     * Modify amount for Betconstruct input
     *
     * FUCKING betcounstruct does not accept normal float like as 10, 10.0, etc,
     * and also does not accept "10.00" as string, should be send the 10.00
     * and in math numbers they are same and it's not possible,
     * so we have to add 0.01 to numbers that finished by 0 at the last decimal number.
     *
     * @param  float $amount
     * @return float
     */
    private function modifyAmount(float $amount): float
    {
        $amount = round($amount, 2);

        $amountString = number_format($amount, 2, null);

        $lastDecimalDigit = (int) substr(explode(".", $amountString)[1], 1, 1);

        $amount = $lastDecimalDigit == 0 ? $amount + 0.01 : $amount;

        return (float) number_format($amount, 2, '.', '');
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

        if (count($requiredParams) < 3) {

            $this->setResponse(JsonResponseHelper::errorResponse(
                null,
                'This request must have at least 3 data more than ID.',
                HttpResponseStatusCode::BadRequest->value,
            ));
            $this->logApiResponse($requiredParams);

            return $this;
        }

        if ($this->params[ClientBalanceCorrectionModelEnum::Amount->name] <= 0) {

            $this->setResponse(JsonResponseHelper::errorResponse(
                null,
                'This amount must greater than 0.',
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
        return 'Client/CreateClientBalanceCorrection';
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
