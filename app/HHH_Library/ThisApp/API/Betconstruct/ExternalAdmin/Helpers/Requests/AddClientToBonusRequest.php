<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests;

use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Balance\ClientBalanceCorrectionModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bonus\AddClientToBonusModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\SuperApiRequest;

class AddClientToBonusRequest extends SuperApiRequest
{

    /**
     * Based on Betconstruct ExternalAdmin-API Documentation
     *
     */

    /*
        Sample Command

        CUrl:

        $url= "http://agp-externaladmin.betconstruct.com/api/en/934/Bonus/AddClientToBonus";
        $body = "{"ClientId":101452623,"CurrencyId":"IRT","Amount":10.01,"DocumentType":301,"Info":"test","RequestHash":"4999b85deff9ff253c32113a36eba6e96b99efe1a221bf459dae7741c5afda21"}";
        $headers = ["Content-Type" => "application/json"];
     */

    /*
        Sample API Server Response

        {
            "StatusCode": "0",
            "Data": {
                        "Id": 628754328,
                        "ClientId": 101452623,
                        "PartnerBonusId": 171911,
                        "Name": "کاربر برنز: شرط رایگان قدردانی‌",
                        "Description": "توضیحات: ۱- برای استفاده، شرط مورد نظر خود را انتخاب کنید. از داخل برگه شرط بندی کنار ستاره بنفش شرط رایگان را روشن کنید. اگر چند شرط رایگان داشته باشید، میتوانید یکی‌ را انتخاب کنید. ۲- در صورت برد، تنها مبلغ برد به حساب شما اضافه میشود. ۳- در صورت برگشت یا لغو شرط، شرط رایگان حذف میشود. ۴- با شرط رایگان می‌توان هر مبلغی برنده شد. ۵- شرط رایگان قابل استفاده در تمامی‌ ورزش‌ها و انواع شرط میباشد.",
                        "Amount": 10.01,
                        "CreatedStamp": 1709014186,
                        "ExpirationDateStamp": 1709446186,
                        "RequestHash": null
                    }
        }


    */

    // Result params
    const Data = "Data";


    private $headers = [];
    private $params = [];

    /**
     * __construct
     *
     * @param  int|string $clientId Partner client ID
     * @param  int $partnerBonusId
     * @param  float $amount
     * @param  bool $autoAccept (optional) If this field is true the bonus will be accepted on player’s behalf
     * @return void
     */
    function __construct(int|string $clientId, int $partnerBonusId,  float $amount, bool $autoAccept = false)
    {
        $this->params[AddClientToBonusModelEnum::ClientId->name] = AddClientToBonusModelEnum::ClientId->cast($clientId);
        $this->params[AddClientToBonusModelEnum::PartnerBonusId->name] = AddClientToBonusModelEnum::PartnerBonusId->cast($partnerBonusId);
        $this->params[AddClientToBonusModelEnum::Amount->name] = $this->modifyAmount($amount);
        $this->params[AddClientToBonusModelEnum::AutoAccept->name] = AddClientToBonusModelEnum::AutoAccept->cast($autoAccept);
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
        return 'Bonus/AddClientToBonus';
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
