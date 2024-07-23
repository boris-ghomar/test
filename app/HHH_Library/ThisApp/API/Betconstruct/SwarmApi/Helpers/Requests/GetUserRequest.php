<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests;

use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ClientSwarmModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\RequestParamsEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\SessionEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\SuperApiRequest;

class GetUserRequest extends SuperApiRequest
{

    /**
     * Based on Betconstruct Swarm-API Documentation
     */

    /*
        Sample Command

        {
            "command": "get_user",
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
                        "auth_token": "6FFD538449DCF6CBC0D29A0B68F1B849",
                        "authentication_status": 0,
                        "balance": 212600.12,
                        "birth_date": "1984-02-25",
                        "bonus_balance": 0.0,
                        "bonus_money": 0.0,
                        "bonus_win_balance": 0.0,
                        "casino_balance": null,
                        "casino_bonus": 0.0,
                        "casino_bonus_win": 0.0,
                        "casino_unplayed_balance": null,
                        "client_notifications": null,
                        "counter_offer_min_amount": 5000000.0,
                        "country_code": "ZW",
                        "currency": "TOM",
                        "currency_name": "TOM",
                        "deposit_count": 8,
                        "doc_region_id": 0,
                        "email": "ferhadtom@betcartemail.com",
                        "first_name": "ferhad",
                        "frozen_balance": 0.0,
                        "has_free_bets": false,
                        "incorrect_fields": null,
                        "is_agent": false,
                        "is_bonus_allowed": true,
                        "is_cash_out_available": true,
                        "is_gdpr_passed": true,
                        "is_mobile_phone_verified": false,
                        "is_phone_verified": false,
                        "is_super_bet_available": true,
                        "is_tax_applicable": false,
                        "is_two_factor_authentication_enabled": null,
                        "is_verified": false,
                        "language": "ar",
                        "last_login_date": 1687511450,
                        "last_login_ip": "185.85.241.71",
                        "last_name": "toman",
                        "loyalty_earned_points": 0.0,
                        "loyalty_exchanged_points": 0.0,
                        "loyalty_last_earned_points": 0.0,
                        "loyalty_level_id": 362,
                        "loyalty_max_exchange_point": 0,
                        "loyalty_min_exchange_point": 0,
                        "loyalty_point": 0.0,
                        "loyalty_point_usage_period": 0,
                        "name": "ferhad ",
                        "personal_id": null,
                        "phone": "00989911200505",
                        "reg_date": "2020-05-05",
                        "site_id": 934,
                        "sport_bonus": 0.0,
                        "sportsbook_profile_id": 6,
                        "status": 1,
                        "subscribe_to_bonus": true,
                        "subscribe_to_email": true,
                        "subscribe_to_internal_message": true,
                        "subscribe_to_phone_call": true,
                        "subscribe_to_push_notification": true,
                        "subscribe_to_sms": true,
                        "subscribed_to_news": false,
                        "terms_and_conditions_acceptance_date": null,
                        "terms_and_conditions_version": null,
                        "unique_id": 222164184,
                        "unplayed_balance": 155800.12,
                        "unread_count": 0,
                        "user_id": 222164184,
                        "username": "ferhadtom",
                        "zip_code": null
                    }
        }
    */

    // Result params
    const Data = "data";


    private $headers = [];
    private $params = [];

    /**
     * Constructor
     * Login required in session
     *
     * @return void
     */
    function __construct()
    {
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
        return 'get_user';
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
        // Needs to login before
        $swarmSessionId = SessionEnum::SwarmSessionId->getSession();

        $this->headers[RequestParamsEnum::SwarmSession->value] = $swarmSessionId;
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
     * Get Item from response data
     *
     * @return mixed
     */
    public function getItem(ClientSwarmModelEnum $clientSwarmModelEnum): mixed
    {

        $value = $this->getParamValue($this->getData(), $clientSwarmModelEnum->dbName());
        return $clientSwarmModelEnum->cast($value);
    }


    /*************************** request getter functions  END ***************************/
}
