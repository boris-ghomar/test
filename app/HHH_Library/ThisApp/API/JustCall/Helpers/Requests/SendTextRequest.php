<?php

namespace App\HHH_Library\ThisApp\API\JustCall\Helpers\Requests;

use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\JustCall\Helpers\SuperApiRequest;

class SendTextRequest extends SuperApiRequest
{

    /**
     * Based on JustCall API Documentation
     *
     * https://justcall.io/developer-docs/#send_text
     *
     */

    /*
        Sample Response:

        {
            "status":"success",
            "message":"Text sent",
            "id":11024,
            "data":
            {
                 "id": 11024,
                "client_number": "91700XXXXXXX",
                "justcall_number": "183XXXXXXX",
                "body": "Hello!",
                "direction": "0",
                "is_mms": "0",
                "mms": null,
                "contact_name": " ",
                "datetime": "2021-08-06 14:53:38",
                "agent_name": "Agile JustCall",
                "agent_id": 10400,
                "delivery_status": "",
                "is_deleted": false
            }
        }
*/
    // Result params
    const Data = "data";


    private $headers = [];
    private $params = [];

    /**
     * Constructor
     *
     * @param  string $to
     * @param  string $body
     * @return void
     */
    function __construct(string $to, string $body)
    {
        $this->params = [
            "from"  => $this->modifyPhoneNumberFormat(AppTechnicalSettingsEnum::JuCaAp_PhoneNumberForSMS->getValue()),
            "to"    => $this->modifyPhoneNumberFormat($to),
            "body"  => $body,
        ];
    }

    /**
     * @override
     * Send request to server
     *
     * @return self
     */
    public function send(): self
    {
        $requiredParams = $this->requiredParams();

        $error = null;
        if (empty($this->params['from']))
            $error = 'This request must include a phone number for the "from" attribute.';
        else if (empty($this->params['to']))
            $error = 'This request must include a phone number for the "to" attribute.';
        else if (empty($this->params['body']))
            $error = 'This request must include a text for the "body" attribute.';

            // Remove extra line break, \n is enough
        $this->params['body'] = str_replace("\r", "", $this->params['body']);

        if (!is_null($error)) {

            $this->setResponse(JsonResponseHelper::errorResponse(
                null,
                $error,
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
        return 'texts/new';
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
