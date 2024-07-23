<?php

namespace App\Enums\General;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum ModelGlobalScopesEnum
{
    use EnumActions;

        // Admin Panel Models
    case Notification_Notifiable;
    case Personnel_Only;
    case PersonnelProfile_AuthProfile;
    case PersonnelRole_Only;
    case UserSetting_UserPersonalSetting;
    case ClientCategory_Only;
    case PostCategory_Only;
    case PostSpace_Only;
    case ArticlePost_Only;
    case FaqPost_Only;
    case PinnedPost_Only;
    case OpenTicket_Only;
    case UnapprovedComment_Only;
    case Domain_Prepairing;
    case Domain_Reported;

        // Site Models
    case UserBetconstruct_Only;
    case UserBetconstructProfile_AuthProfile;
    case MyChatbotChat_Only;
    case MyTicket_Only;
    case MyReferral_Only;
}
