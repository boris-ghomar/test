<?php

namespace App\Http\Controllers\Site\Referral;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\BetsTableEnum;
use App\Enums\Database\Tables\ReferralBetsConclusionsTableEnum;
use App\Enums\Database\Tables\ReferralClaimedRewardsTableEnum;
use App\Enums\Database\Tables\ReferralRewardConclusionsTableEnum;
use App\Enums\Database\Tables\ReferralRewardItemsTableEnum;
use App\Enums\Database\Tables\ReferralRewardPackagesTableEnum;
use App\Enums\Database\Tables\ReferralRewardPaymentsTableEnum;
use App\Enums\Database\Tables\ReferralSessionsTableEnum;
use App\Enums\Database\Tables\ReferralsTableEnum;
use App\Enums\General\CurrencyEnum;
use App\Enums\General\WeekDayEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Session\GeneralSessionsEnum;
use App\HHH_Library\Charts\ChartJs\Configs\Charts\ChartJsLine;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\BorderConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\DataConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\GridConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\LabelsConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\LegendConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\LineConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\OptionsConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\ScaleConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\Themes\DataSetConfigThemesEnum;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\TicksConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\TitleConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\TooltipsConfig;
use App\HHH_Library\general\php\CarbonTimeDiffForHuman;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Models\BackOffice\Referral\Referral;
use App\Models\BackOffice\Referral\ReferralBetsConclusion;
use App\Models\BackOffice\Referral\ReferralClaimedReward;
use App\Models\BackOffice\Referral\ReferralRewardConclusion;
use App\Models\BackOffice\Referral\ReferralRewardItem;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use App\Models\BackOffice\Referral\ReferralRewardPayment;
use App\Models\BackOffice\Referral\ReferralSession;
use App\Models\Site\Referral\ReferralPanel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReferralPanelController extends SuperController
{
    private const STATISTICS_CACHE_TIME = 30; // Based on minutes
    private const REFERRED_CHART_CACHE_TIME = 30; // Based on minutes
    private const REWARD_CHART_CACHE_TIME = 1; // Based on hours

    private const CACHE_CREATED_AT = "cache_created_at";
    private const COLLECTION = "collection", SELECTED_ITEMS = "selectedItems", IS_CLAIMABLE = "isClaimable", CALCULATED_UNTIL = "calculatedUntil";

    private User $user;
    private BetconstructClient $userExtra;
    private CurrencyEnum $userCurrency;
    private ReferralSession|null $inProgressReferralSession = null;
    private ReferralRewardPackage|null $referralRewardPackage = null;
    private ReferralRewardConclusion|null $inProgressReferralRewardConclusion = null;

    /**
     * Referral panel index
     *
     * @return view
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, ReferralPanel::class);

        $this->init();

        $clientReferralData = ReferralPanel::firstOrCreate([ReferralsTableEnum::UserId->dbName() => User::authUser()->id]);

        $data = [
            'referralLink' => SitePublicRoutesEnum::Referral_Link->url(['referredBy' => $clientReferralData[ReferralsTableEnum::ReferralId->dbName()]]),
        ];

        $data = array_merge($data, $this->makeReferredPerformanceChartView());
        $data = array_merge($data, $this->makeRewardPerformanceChartView());
        $data = array_merge($data, $this->makeStatisticsData());
        $data = array_merge($data, $this->getInProgressSessionCardData());

        return view('hhh.Site.pages.ReferralPanel.index', $data);
    }

    /**
     * Claim reward by client
     *
     * @param  \Illuminate\Http\Request $request
     * @return \\Illuminate\Http\RedirectResponse
     */
    public function claimReward(Request $request)
    {
        $this->init();

        $claimedRewardIds = $request->input('claimedRewards');

        $userId = $this->user->id;
        $referralRewardPackageId = $this->referralRewardPackage->id;
        $inProgressReferralSessionId = $this->inProgressReferralSession->id;

        if (!is_null($claimedRewardIds)) {

            $packageClaimableCount = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::ClaimCount->dbName()];

            if (count($claimedRewardIds) <= $packageClaimableCount) {

                $packageIdCol = ReferralRewardItemsTableEnum::PackageId->dbName();
                $isActiveCol = ReferralRewardItemsTableEnum::IsActive->dbName();

                $userIdCol = ReferralClaimedRewardsTableEnum::UserId->dbName();
                $referralSessionIdCol = ReferralClaimedRewardsTableEnum::ReferralSessionId->dbName();
                $rewardItemIdCol = ReferralClaimedRewardsTableEnum::RewardItemId->dbName();

                foreach ($claimedRewardIds as $claimedRewardId) {

                    if ($referralRewardItem = ReferralRewardItem::find($claimedRewardId)) {


                        if ($referralRewardItem[$isActiveCol] && $referralRewardItem[$packageIdCol] == $referralRewardPackageId) {

                            ReferralClaimedReward::firstOrCreate([
                                $userIdCol => $userId,
                                $referralSessionIdCol => $inProgressReferralSessionId,
                                $rewardItemIdCol => $claimedRewardId,
                            ]);
                        }
                    }
                }

                // Delete extra items
                ReferralClaimedReward::where(ReferralClaimedRewardsTableEnum::UserId->dbName(), $userId)
                    ->where(ReferralClaimedRewardsTableEnum::ReferralSessionId->dbName(), $inProgressReferralSessionId)
                    ->whereNotIn(ReferralClaimedRewardsTableEnum::RewardItemId->dbName(), $claimedRewardIds)
                    ->delete();
            }
        } else {
            // Delete claimed items to return system default
            ReferralClaimedReward::where(ReferralClaimedRewardsTableEnum::UserId->dbName(), $userId)
                ->where(ReferralClaimedRewardsTableEnum::ReferralSessionId->dbName(), $inProgressReferralSessionId)
                ->delete();
        }
        return redirect()->back();
    }

    /**
     * Preparation of basic information
     *
     * @return void
     */
    private function init(): void
    {
        /**************** Collect User data ****************/
        $this->user = User::authUser();
        $this->userExtra = $this->user->userExtra;

        $this->userCurrency = CurrencyEnum::getCase(strtoupper($this->userExtra[ClientModelEnum::CurrencyId->dbName()]));
        /**************** Collect User data END ****************/

        /**************** Collect in progress session data ****************/
        $this->inProgressReferralSession = ReferralSession::where(ReferralSessionsTableEnum::Status->dbName(), ReferralSessionStatusEnum::InProgress->name)
            ->orderBy(ReferralSessionsTableEnum::StartedAt->dbName(), 'asc')
            ->orderBy(ReferralSessionsTableEnum::Id->dbName(), 'asc')
            ->first();
        $inProgressReferralSession = is_null($this->inProgressReferralSession) ? null : $this->inProgressReferralSession->id;
        /**************** Collect in progress session data END ****************/

        /**************** Collect in referral reward package data ****************/
        $this->identifyReferralRewardPackage();
        /**************** Collect in referral reward package data END ****************/

        /**************** Collect in inProgress referral reward conclusion data ****************/
        $this->inProgressReferralRewardConclusion = ReferralRewardConclusion::where(ReferralRewardConclusionsTableEnum::UserId->dbName(), $this->user->id)
            ->where(ReferralRewardConclusionsTableEnum::ReferralSessionId->dbName(), $inProgressReferralSession)
            ->first();
        /**************** Collect in inProgress referral reward conclusion data END ****************/
    }

    /**
     * Identify client referral reward package
     *
     * @return void
     */
    private function identifyReferralRewardPackage(): void
    {
        if (is_null($this->inProgressReferralSession))
            return;

        $rewardPackage = null;

        if ($clientReferralCustomSettings = $this->user->clientReferralCustomSettings)
            $rewardPackage = $clientReferralCustomSettings->referralRewardPackage;

        if (is_null($rewardPackage))
            $rewardPackage = $this->inProgressReferralSession->referralRewardPackage;

        $this->referralRewardPackage = $rewardPackage;
    }

    /**
     * Get cache data from sessions
     *
     * @param  \App\Enums\Session\GeneralSessionsEnum $sessionKey
     * @param  int $keepMinutes
     * @return array
     */
    private function getCacheData(GeneralSessionsEnum $sessionKey, int $keepMinutes): ?array
    {
        $data = null;
        $sessiontData = $sessionKey->getSession();

        if (!is_null($sessiontData)) {

            if (isset($sessiontData[self::CACHE_CREATED_AT])) {

                $sessionCreatedAt = $sessiontData[self::CACHE_CREATED_AT];
                if (Carbon::parse($sessionCreatedAt) > now()->subMinutes($keepMinutes)) {
                    $data = $sessiontData;
                }
            }
        }

        return $data;
    }

    /**
     * Store data in sessions cache
     *
     * @param  \App\Enums\Session\GeneralSessionsEnum $sessionKey
     * @param  array $data
     * @return array
     */
    private function setCacheData(GeneralSessionsEnum $sessionKey, array $data): array
    {
        $data[self::CACHE_CREATED_AT] = now()->toDateTimeString();
        $sessionKey->setSession($data);

        return $data;
    }

    /**
     * Make statistics data
     *
     * @return array
     */
    private function makeStatisticsData(): array
    {
        // Try to load data from session cache
        $data = $this->getCacheData(GeneralSessionsEnum::ReferralPanel_StatisticsData, self::STATISTICS_CACHE_TIME);

        if (!is_null($data))
            return $data;

        $user = User::authUser();
        $userId = $user->id;

        $currentPeriodStart = now()->subDays(30);
        $pastPeriodStart = now()->subDays(60);

        /********************* All Referrals *********************/
        $allReferralsCount = Referral::where(ReferralsTableEnum::ReferredBy->dbName(), $userId)
            ->count();

        $currentTotal = Referral::where(ReferralsTableEnum::ReferredBy->dbName(), $userId)
            ->where(TimestampsEnum::CreatedAt->dbName(), '>', $currentPeriodStart)
            ->count();

        $pastTotal = Referral::where(ReferralsTableEnum::ReferredBy->dbName(), $userId)
            ->where(TimestampsEnum::CreatedAt->dbName(), '>', $pastPeriodStart)
            ->where(TimestampsEnum::CreatedAt->dbName(), '<', $currentPeriodStart)
            ->count();

        $allReferralsChange = $this->calculateChangePercentage($pastTotal, $currentTotal);
        /********************* All Referrals END *********************/

        /********************* Active Referrals *********************/
        $activeReferralsCount = ReferralBetsConclusion::where(ReferralBetsConclusionsTableEnum::ReferrerId->dbName(), $userId)
            ->where(ReferralBetsConclusionsTableEnum::BetsCount->dbName(), '>', 0)
            ->where(ReferralBetsConclusionsTableEnum::CalculatedUntil->dbName(), '>', $currentPeriodStart)
            ->distinct(ReferralBetsConclusionsTableEnum::ReferredId->dbName())
            ->count();


        $pastTotal = ReferralBetsConclusion::where(ReferralBetsConclusionsTableEnum::ReferrerId->dbName(), $userId)
            ->where(ReferralBetsConclusionsTableEnum::BetsCount->dbName(), '>', 0)
            ->where(ReferralBetsConclusionsTableEnum::CalculatedUntil->dbName(), '>', $pastPeriodStart)
            ->where(ReferralBetsConclusionsTableEnum::CalculatedUntil->dbName(), '<', $currentPeriodStart)
            ->distinct(ReferralBetsConclusionsTableEnum::ReferredId->dbName())
            ->count();

        $activeReferralsChange = $this->calculateChangePercentage($pastTotal, $activeReferralsCount);
        /********************* Active Referrals END *********************/

        /********************* Total Reward *********************/
        $totalReward = ReferralRewardPayment::where(ReferralRewardPaymentsTableEnum::UserId->dbName(), $userId)
            ->where(ReferralRewardPaymentsTableEnum::IsSuccessful->dbName(), 1)
            ->where(ReferralRewardPaymentsTableEnum::IsDone->dbName(), 1)
            ->sum(ReferralRewardPaymentsTableEnum::Amount->dbName());

        $currentTotal = ReferralRewardPayment::where(ReferralRewardPaymentsTableEnum::UserId->dbName(), $userId)
            ->where(ReferralRewardPaymentsTableEnum::IsSuccessful->dbName(), 1)
            ->where(ReferralRewardPaymentsTableEnum::IsDone->dbName(), 1)
            ->where(TimestampsEnum::CreatedAt->dbName(), '>', $currentPeriodStart)
            ->sum(ReferralRewardPaymentsTableEnum::Amount->dbName());

        $pastTotal = ReferralRewardPayment::where(ReferralRewardPaymentsTableEnum::UserId->dbName(), $userId)
            ->where(ReferralRewardPaymentsTableEnum::IsSuccessful->dbName(), 1)
            ->where(ReferralRewardPaymentsTableEnum::IsDone->dbName(), 1)
            ->where(TimestampsEnum::CreatedAt->dbName(), '>', $pastPeriodStart)
            ->where(TimestampsEnum::CreatedAt->dbName(), '<', $currentPeriodStart)
            ->sum(ReferralRewardPaymentsTableEnum::Amount->dbName());

        $totalRewardChange = $this->calculateChangePercentage($pastTotal, $currentTotal);
        /********************* Total Reward END *********************/

        $data = [
            'allReferralsCount' => number_format($allReferralsCount),
            'allReferralsChange' => $allReferralsChange,

            'activeReferralsCount' => number_format($activeReferralsCount),
            'activeReferralsChange' => $activeReferralsChange,

            'totalReward' => sprintf("%s %s", number_format($totalReward, 2), $this->userCurrency->name),
            'totalRewardChange' => $totalRewardChange,
        ];

        $this->setCacheData(GeneralSessionsEnum::ReferralPanel_StatisticsData, $data);

        return $data;
    }

    /**
     * Calculate change percentage
     *
     * @param  int $pastTotal
     * @param  int $currentTotal
     * @return string
     */
    private function calculateChangePercentage(int $pastTotal, int $currentTotal): string
    {
        $diff = $currentTotal - $pastTotal;
        if ($diff == 0)
            $change = 0;
        else
            $change = $pastTotal == 0 ? 100 : ($diff * 100) / $pastTotal;

        if ($change < 0)
            $changeText = __('thisApp.Site.ReferralPanel.Statistics.DecreasedBy', ['percentage' => number_format((-1 * $change), 2)]);
        else
            $changeText = __('thisApp.Site.ReferralPanel.Statistics.IncreasedBy', ['percentage' => number_format($change, 2)]);

        return $changeText;
    }

    /**
     * Get in progress session card data
     *
     * @return array
     */
    private function getInProgressSessionCardData(): array
    {
        $showInProgressSessionCard =  !is_null($this->inProgressReferralSession) && !is_null($this->referralRewardPackage);

        return [

            'showInProgressSessionCard' => $showInProgressSessionCard,

            'inProgressSessionStartedAt' => $showInProgressSessionCard ? $this->inProgressReferralSession[ReferralSessionsTableEnum::StartedAt->dbName()] : null,
            'inProgressSessionFinishedAt' => $showInProgressSessionCard ? $this->inProgressReferralSession[ReferralSessionsTableEnum::FinishedAt->dbName()] : null,

            'minBetCountReferrer' => $showInProgressSessionCard ? number_format($this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetCountReferrer->dbName()], 0) : null,
            'minBetOddsReferrer' => $showInProgressSessionCard ? $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetOddsReferrer->dbName()] : null,
            'minBetAmountReferrer' => $showInProgressSessionCard ? sprintf("%s %s", number_format($this->getMinBetAmount(true), 2), $this->userCurrency->name) : null,

            'minBetCountReferred' => $showInProgressSessionCard ? number_format($this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetCountReferred->dbName()], 0) : null,
            'minBetOddsReferred' => $showInProgressSessionCard ? $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetOddsReferred->dbName()] : null,
            'minBetAmountReferred' => $showInProgressSessionCard ? sprintf("%s %s", number_format($this->getMinBetAmount(false), 2), $this->userCurrency->name) : null,

            'inprogressReferralRewardItems' => $showInProgressSessionCard ? $this->getInprogressReferralRewardItems() : [],
            'claimableRewardsCount' => $showInProgressSessionCard ? $this->referralRewardPackage[ReferralRewardPackagesTableEnum::ClaimCount->dbName()] : 0,
        ];
    }

    /**
     * Get min bet amount
     *
     * @param bool $referrerClient true: referrerClient, false: referredClient
     * @return float
     */
    private function getMinBetAmount(bool $referrerClient): float
    {
        if ($referrerClient) {

            $minBetAmountIrr = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetAmountIrrReferrer->dbName()];
            $minBetAmountUsd = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetAmountUsdReferrer->dbName()];
        } else {
            $minBetAmountIrr = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetAmountIrrReferred->dbName()];
            $minBetAmountUsd = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetAmountUsdReferred->dbName()];
        }
        $clientCurrency = $this->userCurrency;

        return match ($clientCurrency) {

            CurrencyEnum::IRR, CurrencyEnum::TOM, CurrencyEnum::IRT
            => CurrencyEnum::IRR->exchange($minBetAmountIrr, $clientCurrency),

            CurrencyEnum::USD => $minBetAmountUsd,

            default => 0
        };
    }

    /**
     * Get in progress referral reward items
     *
     * @return array
     */
    private function getInprogressReferralRewardItems(): array
    {
        $result = [
            self::SELECTED_ITEMS => [],
            self::COLLECTION => [],
            self::IS_CLAIMABLE => false,
        ];

        if (is_null($this->inProgressReferralRewardConclusion))
            return $result;

        $totalEffectiveBetsAmount = $this->inProgressReferralRewardConclusion[ReferralRewardConclusionsTableEnum::TotalEffectiveBetsAmount->dbName()];

        $packageClaimableCount = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::ClaimCount->dbName()];

        $claimedRewardsIds = $this->getClaimedRewardsIds();

        $claimedRewardsCount = count($claimedRewardsIds);

        $limit = $claimedRewardsCount < $packageClaimableCount ? $packageClaimableCount - $claimedRewardsCount : 0;

        if ($limit > 0) {

            $restReferralRewardItems = $this->referralRewardPackage->referralRewardItemsActive()
                ->whereNotIn(ReferralRewardItemsTableEnum::Id->dbName(), $claimedRewardsIds)
                ->orderBy(ReferralRewardItemsTableEnum::PaymentPriority->dbName(), 'asc')
                ->limit($limit)
                ->get();

            foreach ($restReferralRewardItems as $restItem)
                array_push($claimedRewardsIds, $restItem->id);
        }

        $result[self::SELECTED_ITEMS] = $claimedRewardsIds;

        if (count($claimedRewardsIds) > 0) {

            if (count($claimedRewardsIds) > $packageClaimableCount) {
                // Cliam items is more than claimable count

                $allowedItems = $this->referralRewardPackage->referralRewardItemsActive()
                    ->whereIn(ReferralRewardItemsTableEnum::Id->dbName(), $claimedRewardsIds)
                    ->orderBy(ReferralRewardItemsTableEnum::PaymentPriority->dbName(), 'asc')
                    ->limit($packageClaimableCount)
                    ->pluck('id')
                    ->toArray();

                $result[self::SELECTED_ITEMS] = $claimedRewardsIds = $allowedItems;

                // Delete extra items
                ReferralClaimedReward::where(ReferralClaimedRewardsTableEnum::UserId->dbName(), $this->user->id)
                    ->where(ReferralClaimedRewardsTableEnum::ReferralSessionId->dbName(), $this->inProgressReferralSession->id)
                    ->whereNotIn(ReferralClaimedRewardsTableEnum::RewardItemId->dbName(), $allowedItems)
                    ->delete();
            }

            $rewardItems = $this->referralRewardPackage->referralRewardItemsActive()
                ->orderBy(ReferralRewardItemsTableEnum::DisplayPriority->dbName(), 'asc')
                ->get();

            $rewardPercentageCol = ReferralRewardItemsTableEnum::Percentage->dbName();
            $rewardDisplayNameCol = ReferralRewardItemsTableEnum::DisplayName->dbName();

            $isUserEligibleToGetReward = $this->isUserEligibleToGetReward();

            foreach ($rewardItems as $rewardItem) {

                $rewardItemId = $rewardItem->id;

                $percentage = $rewardItem[$rewardPercentageCol] / 100;
                $amount = $percentage * $totalEffectiveBetsAmount;

                if (!$isUserEligibleToGetReward || $amount <= 0.01)
                    $amount = 0;

                $label = sprintf("%s: %s %s", $rewardItem[$rewardDisplayNameCol], number_format($amount, 2), $this->userCurrency->name);


                $result[self::COLLECTION][$rewardItemId] = $label;
            }
        }

        $result[self::IS_CLAIMABLE] = count($result[self::COLLECTION]) > count($result[self::SELECTED_ITEMS]);

        return $result;
    }

    /**
     * Check if the user is eligible for the receive reward or not
     *
     * @return bool
     */
    private function isUserEligibleToGetReward(): bool
    {
        $startedAtCol = ReferralSessionsTableEnum::StartedAt->dbName();
        $finishedAtCol = ReferralSessionsTableEnum::FinishedAt->dbName();

        $minBetAmount = $this->getMinBetAmount(true);
        $minBetOdds = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetOddsReferrer->dbName()];
        $minBetCount = $this->referralRewardPackage[ReferralRewardPackagesTableEnum::MinBetCountReferrer->dbName()];

        $beginDate = $this->inProgressReferralSession->getRawOriginal($startedAtCol);
        $endDate = $this->inProgressReferralSession->getRawOriginal($finishedAtCol);

        $bets = $this->user->clientBets()
            ->where(BetsTableEnum::IsReferralBet->dbName(), 1)
            ->where(BetsTableEnum::Odds->dbName(), '>=', $minBetOdds)
            ->where(BetsTableEnum::Amount->dbName(), '>=', $minBetAmount)
            ->where(BetsTableEnum::CalculatedAt->dbName(), '>=', $beginDate)
            ->where(BetsTableEnum::CalculatedAt->dbName(), '<=', $endDate);

        return $bets->count() >= $minBetCount ? true : false;
    }

    /**
     * Get IDs list of claimed rewards by client
     *
     * @return array
     */
    private function getClaimedRewardsIds(): array
    {
        $rewardItemIdCol =  ReferralClaimedRewardsTableEnum::RewardItemId->dbName();

        $claimedRewards = ReferralClaimedReward::where(ReferralClaimedRewardsTableEnum::UserId->dbName(), $this->user->id)
            ->where(ReferralClaimedRewardsTableEnum::ReferralSessionId->dbName(), $this->inProgressReferralSession->id)
            ->get();


        $referralRewardPackageId = $this->referralRewardPackage->id;
        $list = [];

        /** @var ReferralClaimedReward $claimedReward */
        foreach ($claimedRewards as $claimedReward) {

            if ($claimedreferralRewardItem = $claimedReward->referralRewardItem) {

                if ($claimedreferralRewardItem[ReferralRewardItemsTableEnum::IsActive->dbName()]) {

                    $claimedReferralRewardPackage = $claimedreferralRewardItem->referralRewardPackage;

                    if ($claimedReferralRewardPackage->id == $referralRewardPackageId)
                        array_push($list, $claimedReward[$rewardItemIdCol]);
                    else
                        $claimedReward->delete(); // This reward is not belongs to eligible reward package
                } else
                    $claimedReward->delete(); // This reward is not active
            }
        }

        return $list;
    }

    /**
     * Make referred performance chart view
     *
     * @return array
     */
    private function makeReferredPerformanceChartView(): array
    {
        $chartHtmlContainerId = "ReferredPerformanceChartContainer";

        // Try to load data from session cache
        $chartData = $this->getCacheData(GeneralSessionsEnum::ReferralPanel_ReferredPerformanceChartData, self::REFERRED_CHART_CACHE_TIME);

        if (is_null($chartData)) {
            $chartData = $this->getReferredPerformanceChartData();

            $chartData = $this->setCacheData(GeneralSessionsEnum::ReferralPanel_ReferredPerformanceChartData, $chartData);
        }

        $nextCalculationTime = (new CarbonTimeDiffForHuman($chartData[self::CACHE_CREATED_AT], now()->subMinutes(self::REFERRED_CHART_CACHE_TIME)))
            ->ignoreSuffixes()
            ->getDiff();

        $dataSets = $chartData['dataSets'];

        $currentWeekDataSet = DataSetConfigThemesEnum::Orange->create(__('chart.CurrentWeek'), $dataSets[1]);
        $lastWeekDataSet = DataSetConfigThemesEnum::Gray->create(__('chart.LastWeek'), $dataSets[0]);

        $dataConfig = (new DataConfig())
            ->setLabels(WeekDayEnum::translateNameList($chartData['weekDayNames']))
            ->addDataSet($chartHtmlContainerId, $currentWeekDataSet)
            ->addDataSet($chartHtmlContainerId, $lastWeekDataSet);

        $scaleX = (new ScaleConfig())
            ->setTitle(
                (new TitleConfig())
                    ->setDisplay(true)
                    ->setText(__('chart.Day'))
            )
            ->setBorder(new BorderConfig)
            ->setGrid((new GridConfig)->setDisplay(false))
            ->setTicks((new TicksConfig)->setMaxTicksLimit(7));

        $scaleY = (new ScaleConfig())
            ->setTitle(
                (new TitleConfig())
                    ->setDisplay(true)
                    ->setText(__('chart.Total'))
            )
            ->setBorder(new BorderConfig)
            ->setGrid(new GridConfig)
            ->setTicks((new TicksConfig)->setMaxTicksLimit(5))
            ->setMin(0);

        $legend = (new LegendConfig)
            ->setDisplay(true)
            ->setLabels(new LabelsConfig);

        $options = (new OptionsConfig)
            ->addElementLine((new LineConfig)->setTension(0.4))
            ->addScaleX($scaleX)
            ->addScaleY($scaleY)
            ->addPluginLegend($legend)
            ->addPluginTooltips(new TooltipsConfig);

        $chartConfig = (new ChartJsLine($chartHtmlContainerId))
            ->setContainerCssCols(12)
            ->setCardTitle(__('thisApp.Site.ReferralPanel.ReferredPerformanceChart.CardTitle'))
            ->setCardSubtitle(__('thisApp.Site.ReferralPanel.ReferredPerformanceChart.CardSubtitle'))
            ->setCardFooter(__('thisApp.ChartUpdatePeriod', ['updatePeriod' => trans_choice('general.TimeDisplay.minute', self::REFERRED_CHART_CACHE_TIME, ['value' => self::REFERRED_CHART_CACHE_TIME]), 'nextCalculationTime' => $nextCalculationTime]))
            ->setDataConfig($dataConfig)
            ->setOptions($options);


        return [
            'referredPerformanceChartScript' => $chartConfig->createScript(),
            'referredPerformanceChartView' => $chartConfig->createView(),
        ];
    }

    /**
     * Get referred performance chart data
     *
     * @return array
     */
    private function getReferredPerformanceChartData(): array
    {
        $reportWeeksCount = 2;

        $createdAtCol = TimestampsEnum::CreatedAt->dbName();

        $user = User::authUser();

        $startDate = now()->subWeeks($reportWeeksCount)->setTime(23, 59, 59);

        $dataSets = [];
        $weekData = [];
        $weekDayNames = [];

        for ($i = 0; $i < $reportWeeksCount * 7; $i++) {

            $endDate = Carbon::parse($startDate)->addDay();

            if (count($weekDayNames) < 7) {

                $dayName = $endDate->locale(LocaleEnum::English->value)->dayName;
                if (!in_array($dayName, $weekDayNames))
                    array_push($weekDayNames, $dayName);
            }

            $referralsCount = $user->clientReferrals()
                ->where($createdAtCol, '>', $startDate)
                ->where($createdAtCol, '<=', $endDate)
                ->count();

            array_push($weekData, $referralsCount);

            if (($i + 1) % 7 == 0) {
                array_push($dataSets, $weekData);
                $weekData = [];
            }

            $startDate = $endDate;
        }

        return [
            'weekDayNames' => $weekDayNames,
            'dataSets' => $dataSets,
        ];
    }

    /**
     * Make referred performance chart view
     *
     * @return array
     */
    private function makeRewardPerformanceChartView(): array
    {
        $chartHtmlContainerId = "RewardPerformanceChartContainer";

        // Try to load data from session cache
        $chartData = $this->getCacheData(GeneralSessionsEnum::ReferralPanel_RewardPerformanceChartData, self::REWARD_CHART_CACHE_TIME * 60);

        if (is_null($chartData)) {
            $chartData = $this->getRewardPerformanceChartData();
            $chartData[self::CACHE_CREATED_AT] = now()->toDateTimeString();

            $chartData = $this->setCacheData(GeneralSessionsEnum::ReferralPanel_RewardPerformanceChartData, $chartData);
        }

        $nextCalculationTime = (new CarbonTimeDiffForHuman($chartData[self::CACHE_CREATED_AT], now()->subHours(self::REWARD_CHART_CACHE_TIME)))
            ->ignoreSuffixes()
            ->getDiff();

        $dataSet = DataSetConfigThemesEnum::Orange->create(null, $chartData['dataSet']);

        $dataConfig = (new DataConfig())
            ->setLabels($chartData['days'])
            ->addDataSet($chartHtmlContainerId, $dataSet);

        $scaleX = (new ScaleConfig())
            ->setTitle(
                (new TitleConfig())
                    ->setDisplay(true)
                    ->setText(__('chart.Day'))
            )
            ->setBorder(new BorderConfig)
            ->setGrid((new GridConfig)->setDisplay(false))
            ->setTicks((new TicksConfig)->setMaxTicksLimit(7));

        $scaleY = (new ScaleConfig())
            ->setTitle(
                (new TitleConfig())
                    ->setDisplay(true)
                    ->setText(__('chart.Amount'))
            )
            ->setBorder(new BorderConfig)
            ->setGrid(new GridConfig)
            ->setTicks((new TicksConfig)->setMaxTicksLimit(5))
            ->setMin(0);

        $legend = (new LegendConfig)
            ->setDisplay(false)
            ->setLabels(new LabelsConfig);

        $options = (new OptionsConfig)
            ->addElementLine((new LineConfig)->setTension(0.4))
            ->addScaleX($scaleX)
            ->addScaleY($scaleY)
            ->addPluginLegend($legend)
            ->addPluginTooltips(new TooltipsConfig);

        $chartConfig = (new ChartJsLine($chartHtmlContainerId))
            ->setContainerCssCols(12)
            ->setCardTitle(__('thisApp.Site.ReferralPanel.RewardPerformanceChart.CardTitle'))
            ->setCardSubtitle(__('thisApp.Site.ReferralPanel.RewardPerformanceChart.CardSubtitle'))
            ->setCardFooter(__('thisApp.ChartUpdatePeriod', ['updatePeriod' => trans_choice('general.TimeDisplay.hour', self::REWARD_CHART_CACHE_TIME, ['value' => self::REWARD_CHART_CACHE_TIME]), 'nextCalculationTime' => $nextCalculationTime]))
            ->setDataConfig($dataConfig)
            ->setOptions($options);

        return [
            'rewardPerformanceChartScript' => $chartConfig->createScript(),
            'rewardPerformanceChartView' => $chartConfig->createView(),
        ];
    }

    /**
     * Get referred performance chart data
     *
     * @return array
     */
    private function getRewardPerformanceChartData(): array
    {
        $reportsCount = 7;

        $days = [];
        $dataSet = [];

        $user = User::authUser();
        $userId = $user->id;

        $lastReferralRewardConclusionIds = ReferralRewardConclusion::where(ReferralRewardConclusionsTableEnum::UserId->dbName(), $userId)
            ->where(ReferralRewardConclusionsTableEnum::IsDone->dbName(), 1)
            ->orderBy(ReferralRewardConclusionsTableEnum::CalculatedUntil->dbName(), 'desc')
            ->limit($reportsCount)
            ->pluck(ReferralRewardConclusionsTableEnum::Id->dbName())
            ->toArray();

        $referralRewardConclusions = ReferralRewardConclusion::where(ReferralRewardConclusionsTableEnum::UserId->dbName(), $userId)
            ->whereIn(ReferralRewardConclusionsTableEnum::Id->dbName(), $lastReferralRewardConclusionIds)
            ->orderBy(ReferralRewardConclusionsTableEnum::CalculatedUntil->dbName(), 'asc')
            ->limit($reportsCount)
            ->get();

        if (!$referralRewardConclusions->isEmpty()) {

            /** @var ReferralRewardConclusion $referralRewardConclusion */
            foreach ($referralRewardConclusions as $referralRewardConclusion) {

                $referralRewardPaymentsQuery = ReferralRewardPayment::where(ReferralRewardPaymentsTableEnum::UserId->dbName(), $userId)
                    ->where(ReferralRewardPaymentsTableEnum::RewardConclusionsId->dbName(), $referralRewardConclusion->id)
                    ->where(ReferralRewardPaymentsTableEnum::IsDone->dbName(), 1)
                    ->where(ReferralRewardPaymentsTableEnum::IsSuccessful->dbName(), 1)
                    ->orderBy(TimestampsEnum::UpdatedAt->dbName(), 'desc');

                /** @var ReferralRewardPayment $lastPayment*/
                $lastPayment = $referralRewardPaymentsQuery->first();

                if (is_null($lastPayment)) {
                    $lastPaymentDate = $user->convertUTCToLocalTime($referralRewardConclusion->getRawOriginal(ReferralRewardConclusionsTableEnum::CalculatedAt->dbName()), true);
                } else {

                    $lastPaymentDate = $user->convertUTCToLocalTime($lastPayment->getRawOriginal(TimestampsEnum::UpdatedAt->dbName()), true);
                }
                array_push($days, $lastPaymentDate);

                $referralRewardPaymentsSum = $referralRewardPaymentsQuery->sum(ReferralRewardPaymentsTableEnum::Amount->dbName());
                array_push($dataSet, $referralRewardPaymentsSum);
            }
        }

        return [
            'days' => $days,
            'dataSet' => $dataSet,
        ];
    }
}
