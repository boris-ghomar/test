<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin;

use App\Enums\General\CurrencyEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Balance\BalanceCorrectionTypeEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\FilterClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\AddClientToBonusRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\ChangeClientPasswordRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\ClientBalanceCorrectionRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\CreateClientRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetBetsRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetPartnerBonusesRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\UpdateClientRequest;

class ExternalAdminAPI
{

    /************** Api end-points **************/

    /************** Clients information **************/

    /**
     * Get list of clients information
     *
     * Sample:
     * $filter = [
     *       FilterClientModelEnum::Id->name => "62298070",
     *      FilterClientModelEnum::IsLocked->name => "false",
     * ];
     *
     * @param  array $filter
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest
     */
    public static function getClients(array $filter = []): GetClientsRequest
    {
        return (new GetClientsRequest($filter))->send();
    }

    /**
     * Get client by ID
     *
     * @param  int|string $id
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest
     */
    public static function getClientById(int|string $id): GetClientsRequest
    {
        return self::getClients([FilterClientModelEnum::Id->filter($id)]);
    }

    /**
     * Get client by username
     *
     * @param  int|string $id
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest
     */
    public static function getClientByUsername(string $username): GetClientsRequest
    {
        return self::getClients([FilterClientModelEnum::Login->filter($username)]);
    }

    /**
     * Get client by email
     *
     * @param  string $email
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest
     */
    public static function getClientByEmail(string $email): GetClientsRequest
    {
        return self::getClients([FilterClientModelEnum::Email->filter($email)]);
    }

    /**
     * Get client by phone number
     *
     * @param  string $phoneNumber
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest
     */
    public static function getClientByPhoneNumber(string $phoneNumber): GetClientsRequest
    {
        return self::getClients([FilterClientModelEnum::Phone->filter($phoneNumber)]);
    }

    /**
     * Get client by mobile phone
     *
     * @param  string $mobilePhone
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest
     */
    public static function getClientByMobilePhone(string $mobilePhone): GetClientsRequest
    {
        return self::getClients([FilterClientModelEnum::MobilePhone->filter($mobilePhone)]);
    }

    /**
     * Create client
     *
     * @param array $data
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\CreateClientRequest
     */
    public static function createClient(array $data): CreateClientRequest
    {
        return (new CreateClientRequest($data))->send();
    }

    /**
     * Update client data
     *
     * @param  int|string $clientId
     * @param array $data
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\UpdateClientRequest
     */
    public static function updateClient(int|string $clientId, array $data): UpdateClientRequest
    {
        return (new UpdateClientRequest($clientId, $data))->send();
    }

    /**
     * Change client password
     *
     * @param  int|string $clientId
     * @param string $username
     * @param string $newPassword
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\ChangeClientPasswordRequest
     */
    public static function changeClientPassword(int|string $clientId, string $username, string $newPassword): ChangeClientPasswordRequest
    {
        return (new ChangeClientPasswordRequest($clientId, $username, $newPassword))->send();
    }

    /**
     * Get list of bets information
     *
     * Sample:
     * $filter = [
     *      FilterBetModelEnum::ClientId->name => "62298070",
     *      FilterBetModelEnum::MaxRows->name => 10,
     * ];
     *
     * @param  array $filter
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetBetsRequest
     */
    public static function getBets(array $filter = []): GetBetsRequest
    {
        return (new GetBetsRequest($filter))->send();
    }

    /**
     * Get list of partner bonuses information (this is not for client!)
     *
     * Sample:
     * $filter = [
     *      FilterPartnerBonusModelEnum::Type->name => 1,
     * ];
     *
     * @param  array $filter
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetPartnerBonusesRequest
     */
    public static function getPartnerBonuses(array $filter = []): GetPartnerBonusesRequest
    {
        return (new GetPartnerBonusesRequest($filter))->send();
    }

    /**
     * Client balance correction
     *
     * @param  int|string $clientId Partner client ID
     * @param \App\Enums\General\CurrencyEnum $currency
     * @param  float $amount
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Balance\BalanceCorrectionTypeEnum $balanceCorrectionType
     * @param  ?string $info
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\ClientBalanceCorrectionRequest
     */
    public static function clientBalanceCorrection(int|string $clientId, CurrencyEnum $currency, float $amount, BalanceCorrectionTypeEnum $balanceCorrectionType, ?string $info = null): ClientBalanceCorrectionRequest
    {
        return (new ClientBalanceCorrectionRequest($clientId, $currency, $amount, $balanceCorrectionType, $info))->send();
    }

    /**
     * Add client to bonus
     *
     * @param  int|string $clientId Partner client ID
     * @param  int $partnerBonusId
     * @param  float $amount
     * @param  bool $autoAccept (optional) If this field is true the bonus will be accepted on player’s behalf
     * @return \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\AddClientToBonusRequest
     */
    public static function addClientToBonus(int|string $clientId, int $partnerBonusId,  float $amount, bool $autoAccept = false): AddClientToBonusRequest
    {
        return (new AddClientToBonusRequest($clientId, $partnerBonusId, $amount, $autoAccept))->send();
    }

    /************** Clients information END **************/

    /************** Api end-points END **************/
}
