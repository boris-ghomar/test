<?php

namespace App\Enums\Routes;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\general\php\traits\Enums\EnumRoutsAction;
use App\HHH_Library\general\php\traits\TranslateRouteName;

enum AdminRoutesEnum: string implements Translatable
{
    use EnumActions;
    use TranslateRouteName;
    use EnumRoutsAction;

        // GLOBAL ITEMS
    case Global_ViewClientEmail = "Admin.Global.ViewClientMaskedEmail";
    case Global_ViewClientPhone = "Admin.Global.ViewClientMaskedPhone";

        // USERS MANAGEMENT
    case Personnel_Management = "Admin.Personnel.PersonnelManagement";
    case Personnel_RolesManagement = "Admin.Personnel.RolesManagement";
    case Personnel_RolesPermissions = "Admin.Personnel.RolesPermissions";

    case BetconstructClients_Management = "Admin.BetconstructClients.ClientsManagement";
    case Clients_CategoriesManagement = "Admin.Clients.CategoriesManagement";
    case Clients_CategoriesPermissions = "Admin.Clients.CategoriesPermissions";
    case Clients_CategoriesMaps = "Admin.Clients.CategoryMaps";
    case Clients_TrustScores = "Admin.Clients.TrustScores";

        // CONTENT MANAGEMENT
    case PostGrouping_Categories = "Admin.PostGrouping.Categories";
    case PostGrouping_Spaces = "Admin.PostGrouping.Spaces";
    case PostGrouping_PostSpacesPermissions = "Admin.PostGrouping.PostSpacesPermissions";
    case PostGrouping_PostGroupsDisplayPosition = "Admin.PostGrouping.PostGroupsDisplayPosition";

    case Posts_Articles = "Admin.Posts.Articles";
    case Posts_Faq = "Admin.Posts.Faq";
    case Posts_Pinned = "Admin.Posts.Pinned";

    case Comments_Management = "Admin.Comments.Management";
    case UnapprovedComments_Management = "Admin.UnapprovedComments.Management";

        // SUPPORT
    case Chatbots_Bots = "Admin.Chatbots.Bots";
    case Chatbots_Testers = "Admin.Chatbots.Testers";

    case Tickets_AllTickets = "Admin.Tickets.AllTickets";
    case Tickets_OpenTickets = "Admin.Tickets.OpenTickets";

        // PROMOTIONAL
    case Referral_RewardPackages = "Admin.Referral.RewardPackages";
    case Referral_RewardItems = "Admin.Referral.RewardItems";
    case Referral_ReferralSessions = "Admin.Referral.Sessions";
    case Referral_ClientCustomSettings = "Admin.Referral.ClientCustomSettings";
    case Referral_ReferralsManagement = "Admin.Referral.ReferralsManagement";
    case Referral_RewardPayments = "Admin.Referral.RewardPayments";

        // ASSETS
    case Domains_Extensions = "Admin.Domains.Extensions";
    case Domains_Holders = "Admin.Domains.Holders";
    case Domains_HolderAccounts = "Admin.Domains.HolderAccounts";
    case Domains_Categories = "Admin.Domains.Categories";
    case Domains_All = "Admin.Domains.AllDomains";
    case Domains_ImportDomains = "Admin.Domains.ImportDomains";
    case Domains_DomainGenerator = "Admin.Domains.DomainGenerator";
    case Domains_DomainPreparingReview = "Admin.Domains.DomainPreparingReview";
    case Domains_AssignedDomains = "Admin.Domains.AssignedDomains";
    case Domains_AssignedDomainsStatistics = "Admin.Domains.AssignedDomainsStatistics";
    case Domains_ReportedDomains = "Admin.Domains.ReportedDomains";
    case Domains_DedicatedDomains = "Admin.Domains.DedicatedDomains";

        // FINANCIAL
    case Currencies_CurrencyRates = "Admin.Currencies.CurrencyRates";

        // SETTINGS AND SEURITY
    case AccessControl_UnderConstruction = "Admin.AccessControl.UnderConstruction";
    case AccessControl_WhenAdminPanelIsInactive = "Admin.AccessControl.WhenAdminPanelIsInactive";
    case AccessControl_WhenCommunityIsInactive = "Admin.AccessControl.WhenCommunityIsInactive";
    case AccessControl_Permissions = "Admin.AccessControl.Permissions";
    case Settings_GeneralSettings = "Admin.Settings.GeneralSettings";
    case Settings_TechnicalSettings = "Admin.Settings.TechnicalSettings";
    case Settings_DynamicData = "Admin.Settings.DynamicData";

    /**
     *  Default route name by log-viewer package. (php artisan route:list --name=log-viewer)
     * for change route path: App\config\log-viewer.php : 'route_path'
     */
    case SystemReports_SystemLogs = "log-viewer.index";



    /**
     * Get abilities of route
     *
     * @return array
     */
    public function abilities(): array
    {
        $viewAny        = PermissionAbilityEnum::viewAny->name;
        $view           = PermissionAbilityEnum::view->name;
        $create         = PermissionAbilityEnum::create->name;
        $update         = PermissionAbilityEnum::update->name;
        $delete         = PermissionAbilityEnum::delete->name;
        $forceDelete    = PermissionAbilityEnum::forceDelete->name;
        $export         = PermissionAbilityEnum::export->name;

        return match ($this) {

            // GLOBAL ITEMS
            self::Global_ViewClientEmail => [$view],
            self::Global_ViewClientPhone => [$view],

            // USERS MANAGEMENT
            self::Personnel_Management => [$viewAny, $create, $update, $delete],
            self::Personnel_RolesManagement => [$viewAny, $create, $update, $delete],
            self::Personnel_RolesPermissions => [$viewAny, $update],

            self::BetconstructClients_Management => [$viewAny, $update, $delete, $export],
            self::Clients_CategoriesManagement => [$viewAny, $create, $update, $delete],
            self::Clients_CategoriesPermissions => [$viewAny, $update],
            self::Clients_CategoriesMaps => [$viewAny, $create, $update, $delete],
            self::Clients_TrustScores => [$viewAny, $update, $export],

            // CONTENT MANAGEMENT
            self::PostGrouping_Categories => [$viewAny, $create, $update, $delete],
            self::PostGrouping_Spaces => [$viewAny, $create, $update, $delete],
            self::PostGrouping_PostSpacesPermissions => [$viewAny, $update],
            self::PostGrouping_PostGroupsDisplayPosition => [$viewAny, $update],

            self::Posts_Articles => [$viewAny, $create, $update, $delete],
            self::Posts_Faq => [$viewAny, $create, $update, $delete],
            self::Posts_Pinned => [$viewAny, $create, $update, $delete],

            self::Comments_Management => [$viewAny, $update, $delete],
            self::UnapprovedComments_Management => [$viewAny, $update, $delete],

            // SUPPORT
            self::Chatbots_Bots => [$viewAny, $create, $update, $delete],
            self::Chatbots_Testers => [$viewAny, $create, $update, $delete],

            self::Tickets_AllTickets => [$viewAny, $update, $delete],
            self::Tickets_OpenTickets => [$viewAny, $update, $delete],

            // PROMOTIONAL
            self::Referral_RewardPackages => [$viewAny, $create, $update, $delete],
            self::Referral_RewardItems => [$viewAny, $create, $update, $delete],
            self::Referral_ReferralSessions => [$viewAny, $create, $update, $delete],
            self::Referral_ClientCustomSettings => [$viewAny, $create, $update, $delete],
            self::Referral_ReferralsManagement => [$viewAny, $update, $export],
            self::Referral_RewardPayments => [$viewAny, $update, $export],

            // ASSETS
            self::Domains_Extensions => [$viewAny, $create, $update, $delete],
            self::Domains_Holders => [$viewAny, $create, $update, $delete],
            self::Domains_HolderAccounts => [$viewAny, $create, $update, $delete, $export],
            self::Domains_Categories => [$viewAny, $create, $update, $delete],
            self::Domains_All => [$viewAny, $create, $update, $delete, $export],
            self::Domains_ImportDomains => [$viewAny],
            self::Domains_DomainGenerator => [$viewAny],
            self::Domains_DomainPreparingReview => [$viewAny],
            self::Domains_AssignedDomains => [$viewAny, $export],
            self::Domains_AssignedDomainsStatistics => [$viewAny, $export],
            self::Domains_ReportedDomains => [$viewAny, $update, $export],
            self::Domains_DedicatedDomains => [$viewAny, $create, $update, $delete, $export],

            // FINANCIAL
            self::Currencies_CurrencyRates => [$viewAny, $update],

            // SETTINGS AND SEURITY
            self::AccessControl_UnderConstruction => [$viewAny],
            self::AccessControl_WhenAdminPanelIsInactive => [$viewAny],
            self::AccessControl_WhenCommunityIsInactive => [$viewAny],
            self::AccessControl_Permissions => [$viewAny, $update],
            self::Settings_GeneralSettings => [$update],
            self::Settings_TechnicalSettings => [$update],
            self::Settings_DynamicData => [$viewAny, $update],
            self::SystemReports_SystemLogs => [$viewAny, $view, $delete],

            default => []
        };
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

            // GLOBAL ITEMS
            self::Global_ViewClientEmail => $this->transRouteName(['thisApp.GlobalRoutes.MenuTitle', 'thisApp.GlobalRoutes.ViewClientEmail']),
            self::Global_ViewClientPhone => $this->transRouteName(['thisApp.GlobalRoutes.MenuTitle', 'thisApp.GlobalRoutes.ViewClientPhone']),

            // USERS MANAGEMENT
            self::Personnel_Management => $this->transRouteName(['bo_sidebar.Personnel.MenuTitle', 'bo_sidebar.Personnel.Personnel']),
            self::Personnel_RolesManagement => $this->transRouteName(['bo_sidebar.Personnel.MenuTitle', 'bo_sidebar.Personnel.PersonnelRoles']),
            self::Personnel_RolesPermissions => $this->transRouteName(['bo_sidebar.Personnel.MenuTitle', 'bo_sidebar.Personnel.RolesPermissions']),

            self::BetconstructClients_Management => $this->transRouteName(['bo_sidebar.BetconstructClients.MenuTitle', 'bo_sidebar.BetconstructClients.Clients']),
            self::Clients_CategoriesManagement => $this->transRouteName(['bo_sidebar.BetconstructClients.MenuTitle', 'bo_sidebar.BetconstructClients.ClientsCategories']),
            self::Clients_CategoriesPermissions => $this->transRouteName(['bo_sidebar.BetconstructClients.MenuTitle', 'bo_sidebar.BetconstructClients.CategoriesPermissions']),
            self::Clients_CategoriesMaps => $this->transRouteName(['bo_sidebar.BetconstructClients.MenuTitle', 'bo_sidebar.BetconstructClients.CategoriesMaps']),
            self::Clients_TrustScores => $this->transRouteName(['bo_sidebar.BetconstructClients.MenuTitle', 'bo_sidebar.BetconstructClients.ClientTrustScores']),

            // CONTENT MANAGEMENT
            self::PostGrouping_Categories => $this->transRouteName(['bo_sidebar.PostGrouping.MenuTitle', 'bo_sidebar.PostGrouping.Categories']),
            self::PostGrouping_Spaces => $this->transRouteName(['bo_sidebar.PostGrouping.MenuTitle', 'bo_sidebar.PostGrouping.Spaces']),
            self::PostGrouping_PostSpacesPermissions => $this->transRouteName(['bo_sidebar.PostGrouping.MenuTitle', 'bo_sidebar.PostGrouping.PostSpacesPermissions']),
            self::PostGrouping_PostGroupsDisplayPosition => $this->transRouteName(['bo_sidebar.PostGrouping.MenuTitle', 'bo_sidebar.PostGrouping.GroupsDisplayPosition']),

            self::Posts_Articles => $this->transRouteName(['bo_sidebar.Posts.MenuTitle', 'bo_sidebar.Posts.Articles']),
            self::Posts_Faq => $this->transRouteName(['bo_sidebar.Posts.MenuTitle', 'bo_sidebar.Posts.Faq']),
            self::Posts_Pinned => $this->transRouteName(['bo_sidebar.Posts.MenuTitle', 'bo_sidebar.Posts.Pinned']),

            self::Comments_Management => $this->transRouteName(['bo_sidebar.Comments.MenuTitle', 'bo_sidebar.Comments.CommentsManagement']),
            self::UnapprovedComments_Management => $this->transRouteName(['bo_sidebar.Comments.MenuTitle', 'bo_sidebar.Comments.UnapprovedComments']),

            // SUPPORT
            self::Chatbots_Bots => $this->transRouteName(['bo_sidebar.Chatbots.MenuTitle', 'bo_sidebar.Chatbots.ChatbotsManagement']),
            self::Chatbots_Testers => $this->transRouteName(['bo_sidebar.Chatbots.MenuTitle', 'bo_sidebar.Chatbots.ChatbotTesters']),

            self::Tickets_AllTickets => $this->transRouteName(['bo_sidebar.Tickets.MenuTitle', 'bo_sidebar.Tickets.AllTickets']),
            self::Tickets_OpenTickets => $this->transRouteName(['bo_sidebar.Tickets.MenuTitle', 'bo_sidebar.Tickets.OpenTickets']),

            // PROMOTIONAL
            self::Referral_RewardPackages => $this->transRouteName(['bo_sidebar.Referral.MenuTitle', 'bo_sidebar.Referral.RewardPackages']),
            self::Referral_RewardItems => $this->transRouteName(['bo_sidebar.Referral.MenuTitle', 'bo_sidebar.Referral.RewardItems']),
            self::Referral_ReferralSessions => $this->transRouteName(['bo_sidebar.Referral.MenuTitle', 'bo_sidebar.Referral.ReferralSessions']),
            self::Referral_ClientCustomSettings => $this->transRouteName(['bo_sidebar.Referral.MenuTitle', 'bo_sidebar.Referral.ClientCustomSettings']),
            self::Referral_ReferralsManagement => $this->transRouteName(['bo_sidebar.Referral.MenuTitle', 'bo_sidebar.Referral.ReferralsManagement']),
            self::Referral_RewardPayments => $this->transRouteName(['bo_sidebar.Referral.MenuTitle', 'bo_sidebar.Referral.RewardPayments']),

            // ASSETS
            self::Domains_Extensions => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.Extensions']),
            self::Domains_Holders => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.Holders']),
            self::Domains_HolderAccounts => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.HoldersAccounts']),
            self::Domains_Categories => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.Categories']),
            self::Domains_All => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.Domains']),
            self::Domains_ImportDomains => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.ImportDomains']),
            self::Domains_DomainGenerator => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.DomainGenerator']),
            self::Domains_DomainPreparingReview => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.DomainPreparingReview']),
            self::Domains_AssignedDomains => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.AssignedDomains']),
            self::Domains_AssignedDomainsStatistics => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.AssignedDomainsStatistics']),
            self::Domains_ReportedDomains => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.ReportedDomains']),
            self::Domains_DedicatedDomains => $this->transRouteName(['bo_sidebar.Domains.MenuTitle', 'bo_sidebar.Domains.DedicatedDomains']),

            // FINANCIAL
            self::Currencies_CurrencyRates => $this->transRouteName(['bo_sidebar.Currencies.MenuTitle', 'bo_sidebar.Currencies.CurrencyRates']),

            // SETTINGS AND SEURITY
            self::AccessControl_UnderConstruction => $this->transRouteName(['bo_sidebar.AccessControl.UnderConstruction']),
            self::AccessControl_WhenAdminPanelIsInactive => $this->transRouteName(['bo_sidebar.AccessControl.WhenAdminPanelIsInactive']),
            self::AccessControl_WhenCommunityIsInactive => $this->transRouteName(['bo_sidebar.AccessControl.WhenCommunityIsInactive']),
            self::AccessControl_Permissions => $this->transRouteName(['bo_sidebar.AccessControl.MenuTitle', 'bo_sidebar.AccessControl.Permissions']),
            self::Settings_GeneralSettings => $this->transRouteName(['bo_sidebar.Settings.MenuTitle', 'bo_sidebar.Settings.GeneralSettings']),
            self::Settings_TechnicalSettings => $this->transRouteName(['bo_sidebar.Settings.MenuTitle', 'bo_sidebar.Settings.TechnicalSettings']),
            self::Settings_DynamicData => $this->transRouteName(['bo_sidebar.Settings.MenuTitle', 'bo_sidebar.Settings.DynamicData']),
            self::SystemReports_SystemLogs => $this->transRouteName(['bo_sidebar.SystemReports.MenuTitle', 'bo_sidebar.SystemReports.SystemLogs']),

            default => $this->name
        };
    }
}
