<?php

namespace App\Enums\General;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum QueueEnum: string
{
    use EnumActions;

    /**
     * NOTE
     *
     * In case of update, the supervisor config on server
     * must be update to workers work correctly
     */

    case Default = "default"; // [4 workers]

        // Partner fetch queues
    case FetchData = "fetch_data"; // [1 worker] // Fetch foreign data. This queue is used for unscheduled and scattered items.
    case FetchClientBets = "fetch_client_bets"; // [2 workers] // Fetch client bets history from partner
    case UpdateClientUnresultedBets = "update_client_unresulted_bets"; // [1 worker] // Update clients unresulted bets from partner
    case ReferralBetsConclusion = "referral_bets_conclusion"; // [2 workers] // conclusion of referred bets
    case ReferralRewardConclusion = "referral_reward_conclusion"; // [2 workers] // conclusion of referrer reward
    case ReferralRewardPayment = "referral_reward_payment"; // [1 worker] // Deposit rewards to client account
}
