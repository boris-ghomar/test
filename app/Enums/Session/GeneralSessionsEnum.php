<?php

namespace App\Enums\Session;

use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\general\php\traits\Enums\EnumSession;

enum GeneralSessionsEnum: string
{
    use EnumActions;
    use EnumSession;

        // The last url of the page visited by the user
    case LastVisitedPageUrl = "last_visited_page_url";

        // Used in site registration
    case SiteRegistrationFormData = "site_registration_form_data";
    case SiteRegistrationRawData = "site_registration_raw_data";
    case SiteRegistrationStep = "site_registration_step";
    case SiteRegistrationMobileNumber = "site_registration_mobile_number";
    case SiteRegistrationMobileVerificationAttemps = "site_registration_mobile_verification_attemps";
    case SiteRegistrationMobileVerificationLastAttemp = "site_registration_mobile_verification_last_attemp";
    case SiteRegistrationEmail = "site_registration_email";
    case SiteRegistrationEmailVerificationAttemps = "site_registration_email_verification_attemps";
    case SiteRegistrationEmailVerificationLastAttemp = "site_registration_email_verification_last_attemp";
    case SiteRegistrationReferredBy = "site_registration_referred_by";

        // Recovery password
    case SiteRecoveryPasswordMethod = "site_recovery_password_method";
    case SiteRecoveryPasswordUserId = "site_recovery_password_user_id";
    case SiteRecoveryPasswordResetPasswordHash = "site_recovery_password_reset_password_hash";
    case SiteRecoveryPasswordVerifiable = "site_recovery_password_verifiable";
    case SiteRecoveryPasswordVerificationAttempsMobile = "site_recovery_password_verification_attemps_mobile";
    case SiteRecoveryPasswordVerificationLastAttempMobile = "site_recovery_password__verification_last_attemp_mobile";
    case SiteRecoveryPasswordVerificationAttempsEmail = "site_recovery_password_verification_attemps_email";
    case SiteRecoveryPasswordVerificationLastAttempEmail = "site_recovery_password__verification_last_attemp_email";

        // Referral panel
    case ReferralPanel_ReferredPerformanceChartData = "referral_panel_referred_performance_chart_data";
    case ReferralPanel_RewardPerformanceChartData = "referral_panel_reward_performance_chart_data";
    case ReferralPanel_StatisticsData = "referral_panel_statistics_data";
    /******************** static methods ********************/

    /**
     * Forget registration sessions
     *
     * @param bool $fullReset
     * @return void
     */
    public static function forgetRegistrationSessions(bool $fullReset = false): void
    {
        $antiSpamCases = [
            self::SiteRegistrationMobileVerificationAttemps,
            self::SiteRegistrationMobileVerificationLastAttemp,
            self::SiteRegistrationEmailVerificationAttemps,
            self::SiteRegistrationEmailVerificationLastAttemp,
        ];

        $softCases = [
            self::SiteRegistrationFormData,
            self::SiteRegistrationRawData,
            self::SiteRegistrationStep,
            self::SiteRegistrationMobileNumber,
            self::SiteRegistrationEmail,
        ];

        $cases = $fullReset ? array_merge($softCases, $antiSpamCases) : $softCases;

        foreach ($cases as $case)
            $case->forgetSession();
    }

    /**
     * Forget registration sessions
     *
     * @param bool $fullReset
     * @return void
     */
    public static function forgetRecoveryPasswordSessions(bool $fullReset = false): void
    {
        $antiSpamCases = [
            self::SiteRecoveryPasswordVerificationAttempsMobile,
            self::SiteRecoveryPasswordVerificationLastAttempMobile,
            self::SiteRecoveryPasswordVerificationAttempsEmail,
            self::SiteRecoveryPasswordVerificationLastAttempEmail,
        ];

        $softCases = [
            self::SiteRecoveryPasswordMethod,
            self::SiteRecoveryPasswordUserId,
            self::SiteRecoveryPasswordResetPasswordHash,
            self::SiteRecoveryPasswordVerifiable,
            self::SiteRecoveryPasswordVerificationAttempsMobile,
            self::SiteRecoveryPasswordVerificationLastAttempMobile,
            self::SiteRecoveryPasswordVerificationAttempsEmail,
            self::SiteRecoveryPasswordVerificationLastAttempEmail,
        ];

        $cases = $fullReset ? array_merge($softCases, $antiSpamCases) : $softCases;

        foreach ($cases as $case)
            $case->forgetSession();
    }
    /******************** static methods END ********************/
}
