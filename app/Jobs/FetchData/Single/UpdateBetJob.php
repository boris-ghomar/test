<?php

namespace App\Jobs\FetchData\Single;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\BetSelectionsTableEnum;
use App\Enums\Database\Tables\BetsTableEnum;
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
use App\Constants\DelayConstants;
use App\Models\BackOffice\Bets\Bet;
use App\Models\BackOffice\Bets\BetSelection;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UpdateBetJob implements ShouldQueue
{
    /**
     * Update the data of a specific bet from the partner.
     */

    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $betId;

    /**
     * Create a new job instance.
     *
     * @param null|int $userId
     */
    public function __construct(?int $betId)
    {
        $this->onQueue(QueueEnum::UpdateClientUnresultedBets->value);

        $this->betId = $betId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        if (!AppSettingsEnum::IsCommunityActive->getValue()) {

            self::dispatch($this->betId)->delay(now()->addMinutes(DelayConstants::CommunityIsNotActive));
            return;
        }

        try {

            $bet = Bet::find($this->betId);

            if (is_null($bet)) return;

            $filter = [
                FilterBetModelEnum::BetId->filter($bet[BetsTableEnum::PartnerBetId->dbName()]),
            ];

            // $getBetsResponse = ApiResponseTest::getBets();
            $getBetsResponse = ExternalAdminAPI::getBets($filter);

            if ($getBetsResponse->getStatus()->name === ApiStatusEnum::Success->name) {

                $clientBets = (new Collection($getBetsResponse->getData()));

                $this->updateClientBet($clientBets, $bet);
            } else {
                self::dispatch($this->betId)->delay(now()->addMinutes(DelayConstants::PartnerFailedResult));
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Fetch bet data job issue"
            );
        }
    }

    /**
     * Update client bet
     *
     * @param  null|\Illuminate\Support\Collection $clientBets
     * @param \App\Models\BackOffice\Bets\Bet $bet
     * @return void
     */
    private function updateClientBet(?Collection $partnerBets, Bet $bet): void
    {
        if (is_null($partnerBets)) return;

        $partner = PartnerEnum::getCase($bet[BetsTableEnum::Partner->dbName()]);
        if (is_null($partner)) return;

        foreach ($partnerBets as $partnerBet) {

            try {

                $partnerBetId = $partnerBet[BetModelEnum::BetId->name];
                $partnerBetSelections = $partnerBet[BetModelEnum::Selections->name];

                if ($bet[BetsTableEnum::PartnerBetId->dbName()] == $partnerBetId) {

                    $bet = BetModelEnum::fillModel($partnerBet, $bet);

                    if (!is_null($bet)) {

                        $bet[TimestampsEnum::UpdatedAt->dbName()] = now(); // To make sure of avoid refetch bet soon
                        $bet[BetsTableEnum::IsQueued->dbName()] = 0; // Exit from update queue
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
                    "Update client bet issue"
                );
            }
        }
    }
}
