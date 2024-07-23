<?php

namespace App\Enums\Routes;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\general\php\traits\Enums\EnumRoutsAction;
use App\HHH_Library\general\php\traits\TranslateRouteName;

enum SitePublicRoutesEnum: string implements Translatable
{
    /**
     * This routes that no need to permission
     */

    use EnumActions;
    use TranslateRouteName;
    use EnumRoutsAction;

    //
    /**
     * Login methods
     * Do not use directly, instead use defaultLogin() function
     */
    case Logout = "logout"; // default name in laravel

    case RegisterBetconstruct = "Site.Register.Betconstruct";
    case RegisterBetconstructGoBack = "Site.Register.Betconstruct.GoBackRequest";

    case ForgotPasswordBetconstruct = "Site.ForgotPassword.Betconstruct";
    case ForgotPasswordRecoveryMethod = "Site.ForgotPassword.RecoveryMethod";
    case ForgotPasswordRecoveryAttemp = "Site.ForgotPassword.RecoveryAttemp";
    case ForgotPasswordVerifiyPage = "Site.ForgotPassword.VerifiyPage";
    case ForgotPasswordVerifiyAttemp = "Site.ForgotPassword.VerifiyAttemp";
    case ForgotPasswordResetPasswordPage = "Site.ForgotPassword.ResetPasswordPage";
    case ForgotPasswordResetPasswordAttemp = "Site.ForgotPassword.ResetPasswordAttemp";

    case LoginBetconstructApi = "Site.Login.Betconstruct.Api";
    case LoginBetconstructWebSocket = "Site.Login.Betconstruct.WebSocket";

    case Support_Chatbot = "Site.Support.Chatbot";
    case Locale = "Site.Locale";
    case MainPage = "Site.MainPage";
    case Search = "Site.Search";
    case Dashboard = "Site.Dashboard";
    case Profile = "Site.Profile";
    case Notifications = "Site.Notifications";
    case Notifications_Delete = "Site.Notifications.Delete";
    case Notifications_DeleteALl = "Site.Notifications.DeleteAll";
    case IpRestriction = "Site.IpRestriction";
    case IpRestrictionRedirect = "Site.IpRestriction.Redirect";

    case PostArticle = "Site.Post.Article";
    case PostFaq = "Site.Post.Faq";
    case PostGroupContentDispaly = "Site.PostGroupContentDispaly";

    case Tickets_TicketShow = "Site.Tickets.TicketShow"; // Permission included in SiteRoutesEnum::Tickets_MyTickets -> create

    case Referral_Link = "Site.Referral.Link";
    case Referral_ClaimReward = "Site.Referral.ClaimReward";

    /**
     * Default Login to site
     *
     * @return self
     */
    public static function defaultLogin(): self
    {

        return self::LoginBetconstructWebSocket;
    }

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {

        return match ($this) {

            self::Dashboard => $this->transRouteName(['bo_sidebar.Site.Dashboard']),
            self::Profile => $this->transRouteName(['general.MyProfile']),
            self::Notifications => $this->transRouteName(['bo_navbar.Notifications.Notifications']),

            self::PostArticle => $this->transRouteName(['thisApp.Post']),
            self::PostFaq => $this->transRouteName(['thisApp.Post']),

            default => $this->name
        };
    }
}
