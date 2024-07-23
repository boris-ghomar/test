<?php

namespace App\Console\Commands\Referral;

use App\Enums\Database\Tables\ReferralSessionsTableEnum;
use App\Enums\Referral\ReferralSessionStatusEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Models\BackOffice\Referral\ReferralSession;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ReferralSessionStatusChangeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'App-Cronjob:referral-session-status-change-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Referral session status change command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /**
         * Notice:
         *
         * Changing the status of paying reward sessions to "finished" is done inside the
         * "ReferralRewardConclusionCommand" command.
         *
         * \App\Console\Commands\Referral\ReferralRewardConclusionCommand
         */

        if (!AppSettingsEnum::IsCommunityActive->getValue())
            return;

        $statusCol = ReferralSessionsTableEnum::Status->dbName();
        $startedAtCol = ReferralSessionsTableEnum::StartedAt->dbName();
        $finishedAtCol = ReferralSessionsTableEnum::FinishedAt->dbName();

        $payingRewardsStatus = ReferralSessionStatusEnum::PayingRewards->name;
        $inProgressStatus = ReferralSessionStatusEnum::InProgress->name;
        $upcomingStatus = ReferralSessionStatusEnum::Upcoming->name;
        $finishedStatus = ReferralSessionStatusEnum::Finished->name;

        $now = now()->toDateTimeString();

        // Check finished in-progress session
        $finishedInProgressSessions = ReferralSession::where($statusCol, $inProgressStatus)
            ->where($finishedAtCol, '<', $now)
            ->orderBy($finishedAtCol, 'asc')
            ->get();

        foreach ($finishedInProgressSessions as $referralSession) {

            $referralSession[$statusCol] = $payingRewardsStatus;
            $referralSession->save();
        }

        // Check finished upcoming sessions (in case of during this session, referral program was off)
        $finishedUpcomingSessions = ReferralSession::where($statusCol, $upcomingStatus)
            ->where($finishedAtCol, '<', $now)
            ->orderBy($finishedAtCol, 'asc')
            ->get();

        foreach ($finishedUpcomingSessions as $referralSession) {

            $referralSession[$statusCol] = $finishedStatus;
            $referralSession->save();
        }


        if (AppSettingsEnum::ReferralIsActive->getValue() || AppSettingsEnum::ReferralIsActiveForTestClients->getValue()) {

            if (!ReferralSession::where($statusCol, $inProgressStatus)->exists()) {
                // Start new session if another session is not in-progress

                $referralSession = ReferralSession::where($statusCol, $upcomingStatus)
                    ->where($startedAtCol, '<=', $now)
                    ->where($finishedAtCol, '>', $now)
                    ->orderBy($startedAtCol, 'asc')
                    ->first();

                if (!is_null($referralSession)) {

                    $referralSession[$statusCol] = $inProgressStatus;
                    $referralSession->save();
                }
            }

            $this->renewLastSession();
        }
    }

    /**
     * Renew last referral session
     *
     * @return void
     */
    private function renewLastSession(): void
    {
        if (AppSettingsEnum::ReferralIsActive->getValue() || AppSettingsEnum::ReferralIsActiveForTestClients->getValue()) {

            if (AppSettingsEnum::ReferralAutoRenewLastSession->getValue()) {

                $nameCol = ReferralSessionsTableEnum::Name->dbName();
                $statusCol = ReferralSessionsTableEnum::Status->dbName();
                $startedAtCol = ReferralSessionsTableEnum::StartedAt->dbName();
                $finishedAtCol = ReferralSessionsTableEnum::FinishedAt->dbName();
                $privateNoteCol = ReferralSessionsTableEnum::PrivateNote->dbName();

                $upcomingStatus = ReferralSessionStatusEnum::Upcoming->name;
                $inProgressStatus = ReferralSessionStatusEnum::InProgress->name;

                if (!ReferralSession::where($statusCol, $upcomingStatus)->exists()) {

                    /*********************** Calculate start and end time ***********************/

                    /** @var ReferralSession $referralSession */
                    $referralSession = ReferralSession::where($statusCol, $inProgressStatus)
                        ->orderBy($finishedAtCol, 'desc')
                        ->first();

                    if (!is_null($referralSession)) {

                        $startedAt = $referralSession->getRawOriginal($startedAtCol);
                        $finishedAt = $referralSession->getRawOriginal($finishedAtCol);

                        $diff = Carbon::parse($startedAt)->diff($finishedAt);

                        $newStartedAt = Carbon::parse($finishedAt)->addSecond()->toDateTimeString();

                        $newFinishedAt = Carbon::parse($finishedAt)
                            ->addYears($diff->y)
                            ->addMonths($diff->m)
                            ->addDays($diff->d)
                            ->addHours($diff->h)
                            ->addMinutes($diff->i)
                            ->addSeconds($diff->s + 1)
                            ->toDateTimeString();
                        /*********************** Calculate start and end time END ***********************/

                        /*********************** Make Name ***********************/
                        $name = Str::of($referralSession[$nameCol])->trim();

                        $newName = null;
                        if ($name->contains("#")) {

                            $index = $name->afterLast("#")->toString();

                            if (is_numeric($index)) {
                                do {
                                    $newName = $name->replaceLast("#" . $index, "#" . (++$index))->toString();
                                } while (ReferralSession::where($nameCol, $newName)->withTrashed()->exists());
                            }
                        }

                        if (is_null($newName)) {

                            $index = 1;
                            do {
                                $newName = sprintf("%s #%d", $name, $index++);
                            } while (ReferralSession::where($nameCol, $newName)->withTrashed()->exists());
                        }
                        /*********************** Make Name END ***********************/

                        $newSession = new ReferralSession();
                        $newSession->fill($referralSession->getRawOriginal());

                        $attributes = $newSession->getAttributes();

                        $attributes[$nameCol] = $newName;
                        $attributes[$startedAtCol] = $newStartedAt;
                        $attributes[$finishedAtCol] = $newFinishedAt;
                        $attributes[$statusCol] = $upcomingStatus;
                        $attributes[$privateNoteCol] = "Automatically renewed by the system.";

                        $newSession->setRawAttributes($attributes);
                        $newSession->save();
                    }
                }
            }
        }
    }
}
