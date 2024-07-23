<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests;

use App\Enums\General\CurrencyEnum;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Balance\BalanceCorrectionTypeEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\AddClientToBonusRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\ChangeClientPasswordRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\ClientBalanceCorrectionRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\CreateClientRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetBetsRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetPartnerBonusesRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\UpdateClientRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum;
use Illuminate\Http\JsonResponse;

class ApiResponseTest
{

    /**
     * makeResponse
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum $response
     * @return \Illuminate\Http\JsonResponse
     */
    private static function makeResponse(TestResponseEnum $response): JsonResponse
    {
        $responseArray = json_decode($response->value, true);
        return JsonResponseHelper::successResponse($responseArray);
    }

    /**
     * Get list of clients information
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest
     */
    public static function getClients(TestResponseEnum|null $response = null): GetClientsRequest
    {
        $response = is_null($response) ? TestResponseEnum::GetClients_Success : $response;
        return (new GetClientsRequest())->testResponse(self::makeResponse($response));
    }

    /**
     * Get one client
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest
     */
    public static function getClient(TestResponseEnum|null $response = null): GetClientsRequest
    {
        $response = is_null($response) ? TestResponseEnum::GetClient_Success : $response;
        return (new GetClientsRequest())->testResponse(self::makeResponse($response));
    }

    /**
     * Create client
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\CreateClientRequest
     */
    public static function createClient(TestResponseEnum|null $response = null): CreateClientRequest
    {
        $response = is_null($response) ? TestResponseEnum::CreateClient_Success : $response;

        return (new CreateClientRequest([]))->testResponse(self::makeResponse($response));
    }

    /**
     * Update client data
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\UpdateClientRequest
     */
    public static function updateClient(TestResponseEnum|null $response = null): UpdateClientRequest
    {
        $response = is_null($response) ? TestResponseEnum::UpdateClient_Success : $response;

        return (new UpdateClientRequest("", []))->testResponse(self::makeResponse($response));
    }

    /**
     * Change client password
     *
     * @param  int|string $clientId
     * @param string $username
     * @param string $newPassword
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\ChangeClientPasswordRequest
     */
    public static function changeClientPassword(int|string $clientId, string $username, string $newPassword, TestResponseEnum|null $response = null): ChangeClientPasswordRequest
    {
        $response = is_null($response) ? TestResponseEnum::ChangeClientPassword_Success : $response;

        return (new ChangeClientPasswordRequest($clientId, $username, $newPassword))->testResponse(self::makeResponse($response));
    }

    /**
     * Get list of bets information
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetBetsRequest
     */
    public static function getBets(TestResponseEnum|null $response = null): GetBetsRequest
    {
        $response = is_null($response) ? TestResponseEnum::GetBets_Success : $response;
        return (new GetBetsRequest())->testResponse(self::makeResponse($response));
    }

    /**
     * Get list of partner bonuses information (this is not for client!)
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetPartnerBonusesRequest
     */
    public static function getPartnerBonuses(TestResponseEnum|null $response = null): GetPartnerBonusesRequest
    {
        $response = is_null($response) ? TestResponseEnum::GetPartnerBonuses_Success : $response;
        return (new GetPartnerBonusesRequest())->testResponse(self::makeResponse($response));
    }

    /**
     * Client balance correction
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\ClientBalanceCorrectionRequest
     */
    public static function clientBalanceCorrection(TestResponseEnum|null $response = null): ClientBalanceCorrectionRequest
    {
        $response = is_null($response) ? TestResponseEnum::clientBalanceCorrection_Success : $response;
        return (new ClientBalanceCorrectionRequest(1, CurrencyEnum::IRT, 10.00, BalanceCorrectionTypeEnum::CorrectionUp))->testResponse(self::makeResponse($response));
    }

    /**
     * Add client to bonus
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum|null $response
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\AddClientToBonusRequest
     */
    public static function addClientToBonus(TestResponseEnum|null $response = null): AddClientToBonusRequest
    {
        $response = is_null($response) ? TestResponseEnum::AddClientToBonus_Success : $response;
        return (new AddClientToBonusRequest(1, 1, 10.00))->testResponse(self::makeResponse($response));
    }
}
