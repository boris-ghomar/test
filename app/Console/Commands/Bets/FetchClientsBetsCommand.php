<?php

namespace App\Console\Commands\Bets;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ClientSyncsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Constants\DelayConstants;
use App\Enums\Database\Tables\JobsTableEnum;
use App\Enums\General\QueueEnum;
use App\Jobs\FetchData\Single\FetchClientBetsJob;
use App\Models\BackOffice\Sync\ClientSync;
use App\Models\General\Job;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FetchClientsBetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:fetch-clients-bets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch clients bets.';

    private const CLIENTS_COUNT_PER_PATCH = 150;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (AppSettingsEnum::IsCommunityActive->getValue()) {

            $this->unlockTimeoutedRecords();

            $clients = $this->getClients();

            $betsSyncStartedAtCol = ClientSyncsTableEnum::BetsSyncStartedAt->dbName();

            /** @var User $client */
            foreach ($clients as $client) {

                $clientSync = $client->clientSync;

                $clientSync[$betsSyncStartedAtCol] = now();
                $clientSync->save();

                FetchClientBetsJob::dispatch($client->id);
            }
        }
    }

    /**
     * Unlock timeouted records
     *
     * @return void
     */
    private function unlockTimeoutedRecords(): void
    {
        $betsSyncStartedAtCol = ClientSyncsTableEnum::BetsSyncStartedAt->dbName();

        $jobsCount = Job::where(JobsTableEnum::Queue->dbName(), QueueEnum::FetchClientBets->value)
            ->count();

        // Min wait to unlock even there is no job to do: DelayConstants::ReferralMinWaitForUnlockTimeoutedJobRecords
        $maxWaitPerJob = 2; // Based on second
        $syncWaitExpireTime = min(now()->subSeconds($jobsCount * $maxWaitPerJob), now()->subMinutes(DelayConstants::ReferralMinWaitForUnlockTimeoutedJobRecords));

        $timeoutItems = ClientSync::whereNotNull($betsSyncStartedAtCol)
            ->where($betsSyncStartedAtCol, '<', $syncWaitExpireTime)
            ->orderBy($betsSyncStartedAtCol, 'asc')
            ->limit(self::CLIENTS_COUNT_PER_PATCH)
            ->get();

        foreach ($timeoutItems as $clientSync) {

            $clientSync[$betsSyncStartedAtCol] = null;
            $clientSync->save();
        }
    }

    /**
     * Get clients
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getClients(): Collection
    {
        $usersTable = DatabaseTablesEnum::Users;
        $clientSyncsTable = DatabaseTablesEnum::ClientSyncs;

        $clients =  User::Clients()
            ->leftJoin($clientSyncsTable->tableName(), ClientSyncsTableEnum::UserId->dbNameWithTable($clientSyncsTable), '=', UsersTableEnum::Id->dbNameWithTable($usersTable))
            ->whereNull(ClientSyncsTableEnum::BetsSyncStartedAt->dbName())
            ->where(function (Builder $query) use ($clientSyncsTable) {

                $query->whereNull(ClientSyncsTableEnum::BetsSyncDate->dbNameWithTable($clientSyncsTable))
                    ->orWhere(ClientSyncsTableEnum::BetsSyncDate->dbNameWithTable($clientSyncsTable), '<', now()->subHours(DelayConstants::FetchClientBets));
            })
            ->select(

                UsersTableEnum::Id->dbNameWithTable($usersTable),
            )
            ->orderBy(ClientSyncsTableEnum::BetsSyncDate->dbNameWithTable($clientSyncsTable), 'asc') // Bets sync date
            ->orderBy(TimestampsEnum::UpdatedAt->dbNameWithTable($usersTable), 'desc') // Users who have been active recently are placed at the top
            ->limit(self::CLIENTS_COUNT_PER_PATCH)
            ->get();

        return $clients;
    }
}
