<?php

namespace App\Console\Commands\Users;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\ExternalAdminAPI;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class UpdateMissedBetconstructClientData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:Update-missed-betconstruct-client-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update missed betconstruct client data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $usersTable = DatabaseTablesEnum::Users;
        $clientExtrasTable = DatabaseTablesEnum::BetconstructClients;

        $clientsWithoutExtra = UserBetconstruct::leftJoin($clientExtrasTable->tableName(), ClientModelEnum::UserId->dbNameWithTable($clientExtrasTable), '=', UsersTableEnum::Id->dbNameWithTable($usersTable))
            ->where(ClientModelEnum::UserId->dbNameWithTable($clientExtrasTable), null)
            ->select(
                UsersTableEnum::Id->dbNameWithTable($usersTable),
                UsersTableEnum::Username->dbNameWithTable($usersTable),
            )
            ->orderBy(UsersTableEnum::Id->dbName(), 'desc')
            ->first();

        if (!is_null($clientsWithoutExtra)) {

            $bcUserId = $clientsWithoutExtra[UsersTableEnum::Username->dbName()];

            // Fetch client data via external admin API
            $clientsResponse = ExternalAdminAPI::getClientById($bcUserId);

            if ($clientsResponse->getStatus()->name === ApiStatusEnum::Success->name) {

                $clientData = (new Collection($clientsResponse->getData()))->first();

                $betconstructClient = ClientModelEnum::fillModel($clientData, BetconstructClient::find($bcUserId));
                $betconstructClient[ClientModelEnum::UserId->dbName()] = $clientsWithoutExtra[UsersTableEnum::Id->dbName()];

                $betconstructClient->save();
            }
        }
    }
}
