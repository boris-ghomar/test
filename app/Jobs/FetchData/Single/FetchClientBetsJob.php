<?php

namespace App\Jobs\FetchData\Single;

use App\Enums\Database\Tables\BetSelectionsTableEnum;
use App\Enums\Database\Tables\BetsTableEnum;
use App\Enums\Database\Tables\ClientSyncsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\General\PartnerEnum;
use App\Enums\General\QueueEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet\BetModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet\BetSelectionModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\Bet\FilterBetModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\ExternalAdminAPI;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\ApiResponseTest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Constants\DelayConstants;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\BackOffice\Bets\Bet;
use App\Models\BackOffice\Bets\BetSelection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class FetchClientBetsJob implements ShouldQueue
{
    /**
     * Fetch the bets of a specific client from the partner.
     */

    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const DELAY_BEFORE_CONTINUE_FETCHING = 1; // Based on minutes
    private const MAX_FETCH_RECORDS_PER_REQUEST = 100;
    private const FETCH_FROM_DATE_DELAY = 30; // Based on minutes

    private $userId;
    private User $user;
    private BetconstructClient $userExtra;

    /**
     * Create a new job instance.
     *
     * @param null|int $userId
     */
    public function __construct(?int $userId)
    {
        $this->onQueue(QueueEnum::FetchClientBets->value);

        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $initRes = $this->init();
        if (is_string($initRes)) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                "Error: " . $initRes,
                "Issue in init function"
            );
            return;
        } else if ($initRes === false)
            return;

        if (!AppSettingsEnum::IsCommunityActive->getValue()) {

            self::dispatch($this->userId)->delay(now()->addMinutes(DelayConstants::CommunityIsNotActive));
            return;
        }

        $this->fetchBets();
    }

    /**
     * Set initial settings
     *
     * @return bool|string
     * true: Ready to start | false: Minor Error (Needs other information and then it will be called again) | string: Major Error message (It cannot be processed)
     */
    private function init(): bool|string
    {
        try {

            /**************** Collect user data ****************/
            $userId = $this->userId;
            /** @var User $user */
            $user = User::find($userId);

            if (is_null($user)) return sprintf("User not found!\User ID: %s", $userId);
            if (!$user->isClient()) return sprintf("User is not client!\User ID: %s", $userId);

            /** @var BetconstructClient $userExtra*/
            $userExtra = $user->userExtra;
            if (is_null($userExtra)) {

                FetchClientExtraDataJob::dispatchSync($userId);
                self::dispatch($userId)->delay(now()->addMinutes(2));
                return false;
            }

            $this->user = $user;
            $this->userExtra = $userExtra;
            /**************** Collect user data END ****************/
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        return true;
    }

    /**
     * Fetch cleint bets
     *
     * @return void
     */
    private function fetchBets(): void
    {
        try {

            $user = $this->user;
            $userId = $user[UsersTableEnum::Id->dbName()];
            $clientId = $user[UsersTableEnum::Username->dbName()];

            $partner = $user->getPartner();
            if (is_null($partner))
                return;

            $fromDate = $this->getFetchSatrtDate(false);

            // Limit added to avoid of extra API calls
            if ($fromDate > now()->subMinutes(self::FETCH_FROM_DATE_DELAY)) {
                $this->updateClientBetSyncStartDate(null);
                return;
            }

            // Limited to one day because BatConstruct does not send more information than this.
            $toDate = Carbon::parse($fromDate)->addDay(); //  Do not use this: $toDate = $fromDate->addDay();

            if ($toDate > now())
                $toDate = now();

            $filter = [
                FilterBetModelEnum::ClientId->filter($clientId),
                FilterBetModelEnum::Date->filter($fromDate->toDateTimeString()),
                FilterBetModelEnum::DateEnd->filter($toDate->toDateTimeString()),
                FilterBetModelEnum::MaxRows->filter(self::MAX_FETCH_RECORDS_PER_REQUEST),
            ];

            // $getBetsResponse = ApiResponseTest::getBets();
            $getBetsResponse = ExternalAdminAPI::getBets($filter);

            if ($getBetsResponse->getStatus()->name === ApiStatusEnum::Success->name) {

                $clientBets = (new Collection($getBetsResponse->getData()));

                $this->registerClientBets($clientBets, $partner);

                if ($clientBets->count() < self::MAX_FETCH_RECORDS_PER_REQUEST) {
                    // Client's bets are fully fetched up to the $toDate date
                    $this->updateClientSyncDate($toDate);

                    if ($toDate < now()->subHours(DelayConstants::FetchClientBets)) {
                        // The rest of the times should be fetched by now.
                        $this->updateClientBetSyncStartDate(now());
                        self::dispatch($userId)->delay(now()->addMinutes(self::DELAY_BEFORE_CONTINUE_FETCHING));
                    } else {
                        // unlock sync
                        $this->updateClientBetSyncStartDate(null);
                    }
                } else {
                    // Client's bets may not have been fully loaded by $toDate, so they must be re-fetched
                    $this->updateClientBetSyncStartDate(now());
                    self::dispatch($userId)->delay(now()->addMinutes(self::DELAY_BEFORE_CONTINUE_FETCHING));
                }
            } else {
                self::dispatch($userId)->delay(now()->addMinutes(DelayConstants::PartnerFailedResult));
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Fetch client bets issue"
            );
        }
    }

    /**
     * Get fetch satrt date
     *
     * @param  bool $returnString
     * @return string|\Carbon\Carbon
     */
    private function getFetchSatrtDate(bool $returnString): Carbon|string
    {
        $user = $this->user;

        $clientSync = $user->clientSync;

        $betsHistoryStart = Carbon::now()->subDays(AppSettingsEnum::BetDaysOfKeepingHistory->getValue() - 2); // Two days gap to avoid fetch bets that will be available only a few hours

        // Get last bet sync date from client's syncs table
        $fromDate = $clientSync[ClientSyncsTableEnum::BetsSyncDate->dbName()];

        if (is_null($fromDate)) {
            // Get last bet sync date from client's last bet date

            $clientLastBet = $user->clientBets()
                ->whereNotNull(BetsTableEnum::PlacedAt->dbName())
                ->orderby(BetsTableEnum::PlacedAt->dbName(), 'desc')
                ->first();

            $fromDate = is_null($clientLastBet) ? null : Carbon::parse($clientLastBet[BetsTableEnum::PlacedAt->dbName()]);
        } else {
            $fromDate = Carbon::parse($fromDate);
        }

        if (!is_null($fromDate)) {

            if ($fromDate < $betsHistoryStart)
                $fromDate = $betsHistoryStart;
        } else {
            // Set last bet sync date from max days of keeping history

            // Default: max date of keeping bets history
            $fromDate = $betsHistoryStart;

            // Check client registration date
            $clientRegistrationDate = $this->userExtra->getRawOriginal(ClientModelEnum::CreatedStamp->dbName());
            if (!is_null($clientRegistrationDate)) {

                $clientRegistrationDate = Carbon::parse($clientRegistrationDate);

                // Client does not have bets history before registration
                if ($clientRegistrationDate > $fromDate)
                    $fromDate = $clientRegistrationDate;
            }
        }

        // BetConstruct sends bets with a date after than the desired start date, so we go 5 seconds ago.
        $fromDate = $fromDate->subSeconds(5);

        return $returnString ? $fromDate->toDateTimeString() : $fromDate;
    }

    /**
     * Register client bets
     *
     * @param  null|\Illuminate\Support\Collection $clientBets
     * @param \App\Enums\General\PartnerEnum $partner
     * @return void
     */
    private function registerClientBets(?Collection $partnerBets, PartnerEnum $partner): void
    {

        if (is_null($this->user)) return;
        if (is_null($partnerBets)) return;

        $userId = $this->user->id;
        $lastBetPlacedAt = null;

        foreach ($partnerBets as $partnerBet) {

            $betPlacedAt = null;

            try {

                $partnerBetId = $partnerBet[BetModelEnum::BetId->name];
                $partnerBetSelections = $partnerBet[BetModelEnum::Selections->name];

                $bet = Bet::PartnerBets($partner)
                    ->where(BetsTableEnum::PartnerBetId->dbName(), $partnerBetId)
                    ->first();

                $bet = BetModelEnum::fillModel($partnerBet, $bet);

                if (!is_null($bet)) {

                    $betPlacedAt = $bet[BetsTableEnum::PlacedAt->dbName()];
                    if (!is_null($betPlacedAt))
                        $betPlacedAt = Carbon::parse($betPlacedAt);

                    $bet[BetsTableEnum::UserId->dbName()] = $userId;

                    $bet->save();

                    $betId = $bet->id;

                    foreach ($partnerBetSelections as $partnerBetSelection) {

                        $partnerBetSelectionId = $partnerBetSelection[BetSelectionModelEnum::SelectionId->name];

                        $betSelection = BetSelection::where(BetSelectionsTableEnum::BetId->dbName(), $betId)
                            ->where(BetSelectionsTableEnum::SelectionId->dbName(), $partnerBetSelectionId)
                            ->first();


                        $betSelection = BetSelectionModelEnum::fillModel($partnerBetSelection, $betSelection);

                        if (!is_null($betSelection)) {

                            $betSelection[BetSelectionsTableEnum::BetId->dbName()] = $betId;

                            $betSelection->save();
                        } else {

                            $error = sprintf(
                                "Error:Failed to convert partner bet selection model to app model\nBet Data:\n%s",
                                json_encode($partnerBet)
                            );

                            LogCreator::createLogAlert(
                                __CLASS__,
                                __FUNCTION__,
                                $error,
                                "Failed to convert partner bet selection model to app model!"
                            );
                        }
                    }
                } else {

                    $error = sprintf(
                        "Error:Failed to convert partner bet model to app model\nBet Data:\n%s",
                        json_encode($partnerBet)
                    );

                    LogCreator::createLogAlert(
                        __CLASS__,
                        __FUNCTION__,
                        $error,
                        "Failed to convert partner bet model to app model!"
                    );
                }
            } catch (\Throwable $th) {

                $error = sprintf(
                    "Error: %s\nBet Data:\n%s",
                    $th->getMessage(),
                    json_encode($partnerBet)
                );

                LogCreator::createLogError(
                    __CLASS__,
                    __FUNCTION__,
                    $error,
                    "Register client bet issue"
                );
            }

            // Betconstruct does not send the collection with palced at sort
            $lastBetPlacedAt = max($lastBetPlacedAt, $betPlacedAt);
        }

        $this->updateClientSyncDate($lastBetPlacedAt);
    }

    /**
     * Update client sync date
     *
     * @param  null|Carbon\Carbon $date
     * @return void
     */
    private function updateClientSyncDate(?Carbon $date): void
    {
        if (is_null($date)) return;

        if (is_null($this->user)) return;
        $user = $this->user;

        try {

            $clientSync = $user->clientSync;
            $clientSync[ClientSyncsTableEnum::BetsSyncDate->dbName()] = $date->toDateTimeString();
            $clientSync->save();
        } catch (\Throwable $th) {

            LogCreator::createLogAlert(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Error on updating client bet history sync date!"
            );
        }
    }

    /**
     * Update client bet sync start date
     *
     * @param  null|\Carbon\Carbon $date
     * @return void
     */
    private function updateClientBetSyncStartDate(?Carbon $date): void
    {
        if (is_null($this->user)) return;
        $user = $this->user;

        $date = empty($date) ? null : $date->toDateTimeString();

        try {

            $clientSync = $user->clientSync;
            $clientSync[ClientSyncsTableEnum::BetsSyncStartedAt->dbName()] = $date;
            $clientSync->save();
        } catch (\Throwable $th) {

            LogCreator::createLogAlert(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Error on updating client bet sync start date!"
            );
        }
    }
}
