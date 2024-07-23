<?php

namespace App\Console;

use App\Console\Commands\Bets\DeleteExpiredBetsHistory;
use App\Console\Commands\Bets\FetchClientsBetsCommand;
use App\Console\Commands\Bets\UpdateClientsUnresultedBetsCommand;
use App\Console\Commands\Chatbot\CloseInactiveChatbotChats;
use App\Console\Commands\Chatbot\DeleteExpiredChatbotChats;
use App\Console\Commands\Chatbot\DeleteUnusedChatbotImageResoponseImages;
use App\Console\Commands\Chatbot\DeleteUnusedChatbotUserInputImages;
use App\Console\Commands\Domains\CheckExpiredDomains;
use App\Console\Commands\Domains\DeleteExpiredAssignedDomains;
use App\Console\Commands\Referral\DeleteExpiredReferralSessionsDataCommand;
use App\Console\Commands\Referral\ReferralBetsConclusionCommand;
use App\Console\Commands\Referral\ReferralRewardConclusionCommand;
use App\Console\Commands\Referral\ReferralRewardConclusionStatusCheckCommand;
use App\Console\Commands\Referral\ReferralRewardPaymentCommand;
use App\Console\Commands\Referral\ReferralSessionStatusChangeCommand;
use App\Console\Commands\Tickets\CloseExpiredWaitingClientTickets;
use App\Console\Commands\Tickets\DeleteExpiredTickets;
use App\Console\Commands\Tickets\DeleteUnusedTicketImages;
use App\Console\Commands\Users\DeleteExpiredVerifications;
use App\Console\Commands\Users\DeleteUnusedProfileImages;
use App\Console\Commands\Users\UpdateMissedBetconstructClientData;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // HHHE
        // php artisan schedule:work

        //  Times based on UTC

        // All finished queue batches that are more than 24 hours old will be pruned
        $schedule->command('queue:prune-batches')->daily();

        /*************** Delete unsed images ***************/

        // Profile Photos
        $schedule->command(DeleteUnusedProfileImages::class)
            ->dailyAt("22:30")
            ->runInBackground()
            ->withoutOverlapping();

        // Chatbot ImageResoponse
        $schedule->command(DeleteUnusedChatbotImageResoponseImages::class)
            ->dailyAt("23:00")
            ->runInBackground()
            ->withoutOverlapping();

        // Chatbot UserInput Images
        $schedule->command(DeleteUnusedChatbotUserInputImages::class)
            ->dailyAt("23:30")
            ->runInBackground()
            ->withoutOverlapping();

        // Tickets Messages Images
        $schedule->command(DeleteUnusedTicketImages::class)
            ->dailyAt("00:00")
            ->runInBackground()
            ->withoutOverlapping();

        /*************** Delete unsed images END ***************/

        /*************** Betconstruct ***************/

        // Update missed betconstruct client data
        $schedule->command(UpdateMissedBetconstructClientData::class)
            ->everyFiveMinutes()
            ->runInBackground()
            ->withoutOverlapping();

        /*************** Betconstruct END ***************/

        /*************** Chatbot ***************/

        // Close inactive chatbot chats
        $schedule->command(CloseInactiveChatbotChats::class)
            ->hourly()
            ->runInBackground()
            ->withoutOverlapping();

        // Delete expired chatbot chats
        $schedule->command(DeleteExpiredChatbotChats::class)
            ->dailyAt("00:30")
            ->runInBackground()
            ->withoutOverlapping();

        /*************** Chatbot END ***************/

        /*************** Ticket ***************/
        $schedule->command(CloseExpiredWaitingClientTickets::class)
            ->hourly()
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command(DeleteExpiredTickets::class)
            ->dailyAt("00:45")
            ->runInBackground()
            ->withoutOverlapping();
        /*************** Ticket END ***************/

        /*************** Domain ***************/
        $schedule->command(CheckExpiredDomains::class)
            ->dailyAt("01:00")
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command(DeleteExpiredAssignedDomains::class)
            ->dailyAt("01:15")
            ->runInBackground()
            ->withoutOverlapping();
        /*************** Domain END ***************/

        /*************** Bet ***************/
        $schedule->command(FetchClientsBetsCommand::class)
            ->everyMinute()
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command(UpdateClientsUnresultedBetsCommand::class)
            ->everyMinute()
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command(DeleteExpiredBetsHistory::class)
            ->dailyAt("01:30")
            ->runInBackground()
            ->withoutOverlapping();
        /*************** Bet END ***************/

        /*************** Referral ***************/
        $schedule->command(ReferralSessionStatusChangeCommand::class)
            ->everyMinute()
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command(ReferralBetsConclusionCommand::class)
            ->everyMinute()
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command(ReferralRewardConclusionCommand::class)
            ->everyMinute()
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command(ReferralRewardPaymentCommand::class)
            ->everyMinute()
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command(ReferralRewardConclusionStatusCheckCommand::class)
            ->everyThirtyMinutes()
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command(DeleteExpiredReferralSessionsDataCommand::class)
            ->dailyAt("01:45")
            ->runInBackground()
            ->withoutOverlapping();
        /*************** Referral END ***************/


        /*************** Verifications END ***************/
        $schedule->command(DeleteExpiredVerifications::class)
            ->hourly()
            ->runInBackground()
            ->withoutOverlapping();

        /*************** Verifications END ***************/
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
