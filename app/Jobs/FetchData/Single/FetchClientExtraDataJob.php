<?php

namespace App\Jobs\FetchData\Single;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\General\QueueEnum;
use App\Enums\Users\UsersTypesEnum;
use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\ExternalAdminAPI;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class FetchClientExtraDataJob implements ShouldQueue
{
    /**
     * Update the data of a specific bet from the partner.
     */

    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId; // user id of referred client

    /**
     * Create a new job instance.
     *
     * @param null|int $userId
     */
    public function __construct(?int $userId)
    {
        $this->onQueue(QueueEnum::FetchData->value);

        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        try {

            /** @var User $user */
            $user = User::find($this->userId);

            if (is_null($user)) return;
            if (!$user->isClient()) return;

            $clientExtra = $user->userExtra;
            if (!is_null($clientExtra)) return;

            if ($user[UsersTableEnum::Type->dbName()] == UsersTypesEnum::BetconstructClient->name) {
                $this->fetchBetconstructClientData($user);
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Fetch client extra data job issue"
            );
        }
    }

    /**
     * Fetch Betconstruct client data
     *
     * @param  \App\Models\User $user
     * @return void
     */
    private function fetchBetconstructClientData(User $user): void
    {
        $bcUserId = $user[UsersTableEnum::Username->dbName()];

        // Fetch client data via external admin API
        $clientsResponse = ExternalAdminAPI::getClientById($bcUserId);

        if ($clientsResponse->getStatus()->name === ApiStatusEnum::Success->name) {

            $clientData = (new Collection($clientsResponse->getData()))->first();

            $betconstructClient = ClientModelEnum::fillModel($clientData, BetconstructClient::find($bcUserId));
            $betconstructClient[ClientModelEnum::UserId->dbName()] = $user[UsersTableEnum::Id->dbName()];

            $betconstructClient->save();
        }
    }
}
