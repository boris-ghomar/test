<?php

namespace App\Constants;

class DelayConstants
{
    const CommunityIsNotActive = 30; // Based on minutes
    const FetchClientBets = 24; // Based on hours
    const PartnerFailedResult = 10; // Based on minutes

    const ReferralMinWaitForUnlockTimeoutedJobRecords = 30; // Based on minutes
    const ReferralBetsConclusionInProgressSession = 10; // Based on hours
    const ReferralRewardConclusionInProgressSession = 15; // Based on hours, 5 hours after BetsConclusion
    const ReferralWaitForBetsFetch = 2; // Based on hours
}
