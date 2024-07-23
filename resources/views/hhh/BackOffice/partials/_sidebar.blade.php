<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ AdminPublicRoutesEnum::Dashboard->route() }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">@lang('bo_sidebar.Dashboard')</span>
            </a>
        </li>

        {{-- USERS MANAGEMENT --}}
        @can('viewAny_Category-UserManagement')
            <li class="nav-item nav-category">@lang('bo_sidebar.SidbarCategories.UsersManagement')</li>

            {{-- Personnel-Management --}}
            @can('viewAny-PersonnelManagement')
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-Personnel" aria-expanded="false"
                        aria-controls="ui-Personnel">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.Personnel') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.Personnel.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-Personnel">
                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Models\BackOffice\PeronnelManagement\Personnel::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Personnel_Management->route() }}">@lang('bo_sidebar.Personnel.Personnel')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\PeronnelManagement\PersonnelRole::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Personnel_RolesManagement->route() }}">@lang('bo_sidebar.Personnel.PersonnelRoles')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\PeronnelManagement\PersonnelPermissionRole::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Personnel_RolesPermissions->route() }}">@lang('bo_sidebar.Personnel.RolesPermissions')</a>
                                </li>
                            @endcan

                        </ul>
                    </div>

                </li>
            @endcan
            {{-- Personnel-Management END --}}

            {{-- Clients-Management --}}
            @can('viewAny-ClientsManagement')
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-Clients" aria-expanded="false"
                        aria-controls="ui-Clients">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.Clients') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.BetconstructClients.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-Clients">
                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Models\BackOffice\ClientsManagement\UserBetconstruct::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::BetconstructClients_Management->route() }}">@lang('bo_sidebar.BetconstructClients.Clients')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\ClientsManagement\ClientCategory::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Clients_CategoriesManagement->route() }}">@lang('bo_sidebar.BetconstructClients.ClientsCategories')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\ClientsManagement\ClientPermissionCategory::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Clients_CategoriesPermissions->route() }}">@lang('bo_sidebar.BetconstructClients.CategoriesPermissions')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\ClientsManagement\ClientCategoryMap::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Clients_CategoriesMaps->route() }}">@lang('bo_sidebar.BetconstructClients.CategoriesMaps')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\ClientsManagement\ClientTrustScore::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Clients_TrustScores->route() }}">@lang('bo_sidebar.BetconstructClients.ClientTrustScores')</a>
                                </li>
                            @endcan

                        </ul>
                    </div>

                </li>
            @endcan
            {{-- Clients-Management END --}}
        @endcan
        {{-- USERS MANAGEMENT END --}}

        {{-- CONTENT MANAGEMENT --}}
        @can('viewAny_Category-PostManagement')

            <li class="nav-item nav-category">@lang('bo_sidebar.SidbarCategories.PostManagement')</li>

            {{-- PostGrouping --}}
            @can('viewAny-PostGrouping')

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-PostGrouping" aria-expanded="false"
                        aria-controls="ui-PostGrouping">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.PostGrouping') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.PostGrouping.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-PostGrouping">
                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Models\BackOffice\PostGrouping\PostCategory::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::PostGrouping_Categories->route() }}">@lang('bo_sidebar.PostGrouping.Categories')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\PostGrouping\PostSpace::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::PostGrouping_Spaces->route() }}">@lang('bo_sidebar.PostGrouping.Spaces')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\PostGrouping\PostSpacePermission::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::PostGrouping_PostSpacesPermissions->route() }}">@lang('bo_sidebar.PostGrouping.PostSpacesPermissions')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\PostGrouping\PostGroupsDisplayPosition::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::PostGrouping_PostGroupsDisplayPosition->route() }}">@lang('bo_sidebar.PostGrouping.GroupsDisplayPosition')</a>
                                </li>
                            @endcan

                        </ul>
                    </div>

                </li>
            @endcan
            {{-- PostGrouping END --}}

            {{-- Posts --}}
            @can('viewAny-Posts')

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-Posts" aria-expanded="false"
                        aria-controls="ui-Posts">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.Posts') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.Posts.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-Posts">
                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Models\BackOffice\Posts\ArticlePost::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Posts_Articles->route() }}">@lang('bo_sidebar.Posts.Articles')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Posts\FaqPost::class)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ AdminRoutesEnum::Posts_Faq->route() }}">@lang('bo_sidebar.Posts.Faq')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Posts\PinnedPost::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Posts_Pinned->route() }}">@lang('bo_sidebar.Posts.Pinned')</a>
                                </li>
                            @endcan

                        </ul>
                    </div>

                </li>
            @endcan
            {{-- Posts END --}}

            {{-- Comments --}}
            @can('viewAny-Comments')

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-Comments" aria-expanded="false"
                        aria-controls="ui-Comments">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.Comments') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.Comments.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-Comments">
                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Models\BackOffice\Comments\Comment::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Comments_Management->route() }}">@lang('bo_sidebar.Comments.CommentsManagement')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Comments\UnapprovedComment::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::UnapprovedComments_Management->route() }}">@lang('bo_sidebar.Comments.UnapprovedComments')</a>
                                </li>
                            @endcan

                        </ul>
                    </div>

                </li>
            @endcan
            {{-- Comments END --}}

        @endcan
        {{-- CONTENT MANAGEMENT END --}}

        {{-- SUPPORT --}}
        @can('viewAny_Category-Support')
            <li class="nav-item nav-category">@lang('bo_sidebar.SidbarCategories.Support')</li>

            {{-- Chatbots --}}
            @can('viewAny-Chatbots')

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-Chatbots" aria-expanded="false"
                        aria-controls="ui-Chatbots">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.Chatbot') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.Chatbots.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-Chatbots">
                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Models\BackOffice\Chatbot\Chatbot::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Chatbots_Bots->route() }}">@lang('bo_sidebar.Chatbots.ChatbotsManagement')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Chatbot\ChatbotTester::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Chatbots_Testers->route() }}">@lang('bo_sidebar.Chatbots.ChatbotTesters')</a>
                                </li>
                            @endcan

                        </ul>
                    </div>

                </li>
            @endcan
            {{-- Chatbots END --}}

            {{-- Tickets --}}
            @can('viewAny-Tickets')

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-Tickets" aria-expanded="false"
                        aria-controls="ui-Tickets">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.Tickets') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.Tickets.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-Tickets">
                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Models\BackOffice\Tickets\Ticket::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Tickets_AllTickets->route() }}">@lang('bo_sidebar.Tickets.AllTickets')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Tickets\OpenTicket::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Tickets_OpenTickets->route() }}">@lang('bo_sidebar.Tickets.OpenTickets')</a>
                                </li>
                            @endcan

                        </ul>
                    </div>

                </li>
            @endcan
            {{-- Tickets END --}}

        @endcan
        {{-- SUPPORT END --}}

        {{-- PROMOTIONAL --}}
        @can('viewAny_Category-Promotional')
            <li class="nav-item nav-category">@lang('bo_sidebar.SidbarCategories.Promotional')</li>

            {{-- Referral --}}
            @can('viewAny-Referral')

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-Referral" aria-expanded="false"
                        aria-controls="ui-Referral">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.Referral') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.Referral.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-Referral">
                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Models\BackOffice\Referral\ReferralRewardPackage::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Referral_RewardPackages->route() }}">@lang('bo_sidebar.Referral.RewardPackages')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Referral\ReferralRewardItem::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Referral_RewardItems->route() }}">@lang('bo_sidebar.Referral.RewardItems')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Referral\ReferralSession::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Referral_ReferralSessions->route() }}">@lang('bo_sidebar.Referral.ReferralSessions')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Referral\ReferralCustomSetting::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Referral_ClientCustomSettings->route() }}">@lang('bo_sidebar.Referral.ClientCustomSettings')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Referral\Referral::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Referral_ReferralsManagement->route() }}">@lang('bo_sidebar.Referral.ReferralsManagement')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Referral\ReferralRewardPayment::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Referral_RewardPayments->route() }}">@lang('bo_sidebar.Referral.RewardPayments')</a>
                                </li>
                            @endcan

                        </ul>
                    </div>

                </li>
            @endcan
            {{-- Referral END --}}

        @endcan
        {{-- PROMOTIONAL END --}}

        {{-- ASSETS --}}
        @can('viewAny_Category-Assets')
            <li class="nav-item nav-category">@lang('bo_sidebar.SidbarCategories.Assets')</li>

            {{-- Domains --}}
            @can('viewAny-Domains')

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-Domains" aria-expanded="false"
                        aria-controls="ui-Domains">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.Domains') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.Domains.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-Domains">
                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Models\BackOffice\Domains\DomainExtension::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Domains_Extensions->route() }}">@lang('bo_sidebar.Domains.Extensions')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Domains\DomainHolder::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Domains_Holders->route() }}">@lang('bo_sidebar.Domains.Holders')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Domains\DomainHolderAccount::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Domains_HolderAccounts->route() }}">@lang('bo_sidebar.Domains.HoldersAccounts')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Domains\DomainCategory::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Domains_Categories->route() }}">@lang('bo_sidebar.Domains.Categories')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Domains\Domain::class)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ AdminRoutesEnum::Domains_All->route() }}">@lang('bo_sidebar.Domains.Domains')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Domains\DomainImport::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Domains_ImportDomains->route() }}">@lang('bo_sidebar.Domains.ImportDomains')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Domains\DomainGenerator::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Domains_DomainGenerator->route() }}">@lang('bo_sidebar.Domains.DomainGenerator')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Domains\DomainPreparingReview::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Domains_DomainPreparingReview->route() }}">@lang('bo_sidebar.Domains.DomainPreparingReview')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Domains\AssignedDomain::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Domains_AssignedDomains->route() }}">@lang('bo_sidebar.Domains.AssignedDomains')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Domains\AssignedDomainsStatistic::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Domains_AssignedDomainsStatistics->route() }}">@lang('bo_sidebar.Domains.AssignedDomainsStatistics')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Domains\ReportedDomain::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Domains_ReportedDomains->route() }}">@lang('bo_sidebar.Domains.ReportedDomains')</a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\BackOffice\Domains\DedicatedDomain::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Domains_DedicatedDomains->route() }}">@lang('bo_sidebar.Domains.DedicatedDomains')</a>
                                </li>
                            @endcan

                        </ul>
                    </div>

                </li>
            @endcan
            {{-- Domains END --}}


        @endcan
        {{-- ASSETS END --}}

        {{-- FINANCIAL --}}
        @can('viewAny_Category-Financial')
            <li class="nav-item nav-category">@lang('bo_sidebar.SidbarCategories.Financial')</li>

            {{-- Currencies --}}
            @can('viewAny-Currencies')

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-Currencies" aria-expanded="false"
                        aria-controls="ui-Currencies">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.Currencies') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.Currencies.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-Currencies">
                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Models\BackOffice\Currencies\CurrencyRate::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Currencies_CurrencyRates->route() }}">@lang('bo_sidebar.Currencies.CurrencyRates')</a>
                                </li>
                            @endcan

                        </ul>
                    </div>

                </li>
            @endcan
            {{-- Currencies END --}}

        @endcan
        {{-- FINANCIAL END --}}

        {{-- SETTINGS AND SEURITY --}}
        @can('viewAny_Category-SettingsAndSecurity')
            <li class="nav-item nav-category">@lang('bo_sidebar.SidbarCategories.SettingsAndSecurity')</li>

            {{-- AccessControl --}}
            @can('viewAny-AccessControl')
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-AccessControl" aria-expanded="false"
                        aria-controls="ui-AccessControl">
                        {{-- <i class="menu-icon mdi mdi-floor-plan"></i> --}}
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.AccessControl') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.AccessControl.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>


                    <div class="collapse" id="ui-AccessControl">

                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Models\BackOffice\AccessControl\Permission::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::AccessControl_Permissions->route() }}">@lang('bo_sidebar.AccessControl.Permissions')</a>
                                </li>
                            @endcan

                        </ul>
                    </div>

                </li>
            @endcan
            {{-- AccessControl END --}}

            {{-- Settings --}}
            @can('viewAny-Settings')
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-settings" aria-expanded="false"
                        aria-controls="ui-settings">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.Settings') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.Settings.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-settings">
                        <ul class="nav flex-column sub-menu">

                            @can('update', App\Models\BackOffice\Settings\Setting::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Settings_GeneralSettings->route() }}">@lang('bo_sidebar.Settings.GeneralSettings')</a>
                                </li>
                            @endCan

                            @can('update', App\Models\BackOffice\Settings\TechnicalSetting::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Settings_TechnicalSettings->route() }}">@lang('bo_sidebar.Settings.TechnicalSettings')</a>
                                </li>
                            @endCan

                            @can('update', App\Models\BackOffice\Settings\DynamicData::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::Settings_DynamicData->route() }}">@lang('bo_sidebar.Settings.DynamicData')</a>
                                </li>
                            @endCan

                        </ul>
                    </div>
                </li>
            @endcan
            {{-- Settings END --}}

            {{-- SystemReports --}}
            @can('viewAny-SystemReports')
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-system_reports" aria-expanded="false"
                        aria-controls="ui-system_reports">
                        <i class="menu-icon {{ config('hhh_config.fontIcons.menu.SystemReports') }}"></i>
                        <span class="menu-title">@lang('bo_sidebar.SystemReports.MenuTitle')</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="ui-system_reports">
                        <ul class="nav flex-column sub-menu">

                            @can('viewAny', App\Policies\BackOffice\SystemReports\SystemLogsPolicy::class)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ AdminRoutesEnum::SystemReports_SystemLogs->route() }}">@lang('bo_sidebar.SystemReports.SystemLogs')</a>
                                </li>
                            @endCan

                        </ul>
                    </div>
                </li>
            @endcan
            {{-- SystemReports END --}}
        @endcan

        {{-- SETTINGS AND SEURITY END --}}

    </ul>
</nav>
