<?php

namespace App\Enums\Routes;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\general\php\traits\Enums\EnumRoutsAction;
use App\HHH_Library\general\php\traits\TranslateRouteName;

enum AdminPublicRoutesEnum: string implements Translatable
{
    use EnumActions;
    use TranslateRouteName;
    use EnumRoutsAction;

    /**
     * This routes no need to permission
     * Or the permission is embedded in the Admin route
     */

        // Auth
    case Login = "Admin.Auth.Login";
    case ForgotPassword = "Admin.Auth.ForgotPassword";
    case ResetPasswordIndex = "Admin.Auth.ResetPassword.Index";
    case ResetPasswordAttempt = "Admin.Auth.ResetPassword.Attempt";

    case Locale = "Admin.Locale";
    case ExportExcel = "Admin.Export.Excel";
    case Dashboard = "Admin.Dashboard";
    case Profile = "Admin.Profile";
    case Notifications = "Admin.Notifications";

        // permission is embedded in AdminRoutesEnum::Posts_Articles
    case Posts_ArticlesCreate = "Admin.Posts.Articles.Create";
    case Posts_ArticlesEdit = "Admin.Posts.Articles.Edit";
    case Posts_FaqCreate = "Admin.Posts.Faq.Create";
    case Posts_FaqEdit = "Admin.Posts.Faq.Edit";

    case Chatbots_EditBot = "Admin.chatbots.EditBot"; // Permissions included in AdminRoutesEnum::Chatbots_Bots->update

    case Ticket_Messenger = "Admin.Tickets.Messenger"; // Permissions included in AdminRoutesEnum::Tickets_X



    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {

        return match ($this) {

            self::Dashboard => $this->transRouteName(['bo_sidebar.Dashboard']),
            self::Profile => $this->transRouteName(['general.MyProfile']),
            self::Notifications => $this->transRouteName(['bo_navbar.Notifications.Notifications']),

            default => $this->name
        };
    }
}
