<?php

namespace App\Providers;

use App\Models\BackOffice\AccessControl\Permission;
use App\Models\BackOffice\Chatbot\Chatbot;
use App\Models\BackOffice\Chatbot\ChatbotTester;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\BackOffice\ClientsManagement\ClientPermissionCategory;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use App\Models\BackOffice\Comments\Comment;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\BackOffice\PeronnelManagement\PersonnelPermissionRole;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole;
use App\Models\BackOffice\PostGrouping\PostCategory;
use App\Models\BackOffice\PostGrouping\PostGroupsDisplayPosition;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\PostGrouping\PostSpacePermission;
use App\Models\BackOffice\Posts\ArticlePost;
use App\Models\BackOffice\Posts\FaqPost;
use App\Models\BackOffice\Settings\Setting;
use App\Models\BackOffice\ClientsManagement\ClientCategoryMap;
use App\Models\BackOffice\ClientsManagement\ClientTrustScore;
use App\Models\BackOffice\Comments\UnapprovedComment;
use App\Models\BackOffice\Currencies\CurrencyRate;
use App\Models\BackOffice\Domains\AssignedDomain;
use App\Models\BackOffice\Domains\AssignedDomainsStatistic;
use App\Models\BackOffice\Domains\DedicatedDomain;
use App\Models\BackOffice\Domains\Domain;
use App\Models\BackOffice\Domains\DomainCategory;
use App\Models\BackOffice\Domains\DomainExtension;
use App\Models\BackOffice\Domains\DomainGenerator;
use App\Models\BackOffice\Domains\DomainHolder;
use App\Models\BackOffice\Domains\DomainHolderAccount;
use App\Models\BackOffice\Domains\DomainImport;
use App\Models\BackOffice\Domains\DomainPreparingReview;
use App\Models\BackOffice\Domains\ReportedDomain;
use App\Models\BackOffice\Posts\PinnedPost;
use App\Models\BackOffice\Referral\Referral;
use App\Models\BackOffice\Referral\ReferralCustomSetting;
use App\Models\BackOffice\Referral\ReferralRewardItem;
use App\Models\BackOffice\Referral\ReferralRewardPackage;
use App\Models\BackOffice\Referral\ReferralRewardPayment;
use App\Models\BackOffice\Referral\ReferralSession;
use App\Models\BackOffice\Settings\DynamicData;
use App\Models\BackOffice\Settings\TechnicalSetting;
use App\Models\BackOffice\Tickets\OpenTicket;
use App\Models\BackOffice\Tickets\Ticket;
use App\Models\Site\Chatbot\MyChatbotChat;
use App\Models\Site\Referral\ReferralPanel;
use App\Models\Site\Tickets\MyTicket;
use App\Policies\BackOffice\AccessControl\PermissionPolicy;
use App\Policies\BackOffice\AccessControl\UnderConstructionPolicy;
use App\Policies\BackOffice\AccessControl\WhenAdminPanelIsInactivePolicy;
use App\Policies\BackOffice\AccessControl\WhenCommunityIsInactivePolicy;
use App\Policies\BackOffice\Chatbot\ChatbotPolicy;
use App\Policies\BackOffice\Chatbot\ChatbotTesterPolicy;
use App\Policies\BackOffice\ClientsManagement\ClientCategoryPolicy;
use App\Policies\BackOffice\ClientsManagement\ClientPermissionCategoryPolicy;
use App\Policies\BackOffice\ClientsManagement\UserBetconstructPolicy;
use App\Policies\BackOffice\Comments\CommentPolicy;
use App\Policies\BackOffice\PeronnelManagement\PersonnelPermissionRolePolicy;
use App\Policies\BackOffice\PeronnelManagement\PersonnelPolicy;
use App\Policies\BackOffice\PeronnelManagement\PersonnelRolePolicy;
use App\Policies\BackOffice\PostGrouping\PostCategoryPolicy;
use App\Policies\BackOffice\PostGrouping\PostGroupsDisplayPositionPolicy;
use App\Policies\BackOffice\PostGrouping\PostSpacePermissionPolicy;
use App\Policies\BackOffice\PostGrouping\PostSpacePolicy;
use App\Policies\BackOffice\Posts\ArticlePostPolicy;
use App\Policies\BackOffice\Posts\FaqPostPolicy;
use App\Policies\BackOffice\Settings\SettingPolicy;
use App\Policies\BackOffice\SystemReports\SystemLogsPolicy;
use App\Policies\BackOffice\ClientsManagement\ClientCategoryMapPolicy;
use App\Policies\BackOffice\ClientsManagement\ClientTrustScorePolicy;
use App\Policies\BackOffice\Comments\UnapprovedCommentPolicy;
use App\Policies\BackOffice\Currencies\CurrencyRatePolicy;
use App\Policies\BackOffice\Domains\AssignedDomainPolicy;
use App\Policies\BackOffice\Domains\AssignedDomainsStatisticPolicy;
use App\Policies\BackOffice\Domains\DedicatedDomainPolicy;
use App\Policies\BackOffice\Domains\DomainCategoryPolicy;
use App\Policies\BackOffice\Domains\DomainExtensionPolicy;
use App\Policies\BackOffice\Domains\DomainGeneratorPolicy;
use App\Policies\BackOffice\Domains\DomainHolderAccountPolicy;
use App\Policies\BackOffice\Domains\DomainHolderPolicy;
use App\Policies\BackOffice\Domains\DomainImportPolicy;
use App\Policies\BackOffice\Domains\DomainPolicy;
use App\Policies\BackOffice\Domains\DomainPreparingReviewPolicy;
use App\Policies\BackOffice\Domains\ReportedDomainPolicy;
use App\Policies\BackOffice\Global\GlobalViewClientEmailPolicy;
use App\Policies\BackOffice\Global\GlobalViewClientPhonePolicy;
use App\Policies\BackOffice\Posts\PinnedPostPolicy;
use App\Policies\BackOffice\Referral\ReferralCustomSettingPolicy;
use App\Policies\BackOffice\Referral\ReferralPolicy;
use App\Policies\BackOffice\Referral\ReferralRewardItemPolicy;
use App\Policies\BackOffice\Referral\ReferralRewardPackagePolicy;
use App\Policies\BackOffice\Referral\ReferralRewardPaymentPolicy;
use App\Policies\BackOffice\Referral\ReferralSessionPolicy;
use App\Policies\BackOffice\Settings\DynamicDataPolicy;
use App\Policies\BackOffice\Settings\TechnicalSettingPolicy;
use App\Policies\BackOffice\Tickets\OpenTicketPolicy;
use App\Policies\BackOffice\Tickets\TicketPolicy;
use App\Policies\Site\Chatbot\MyChatbotChatPolicy;
use App\Policies\Site\Referral\ReferralPanelPolicy;
use App\Policies\Site\Tickets\MyTicketPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Opcodes\LogViewer\LogFile;
use Opcodes\LogViewer\LogFolder;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',

        /******************* HHHE ************************/

        /*--------------------- AdminPanel Policies ---------------------*/

        //These policies has no controllers and models and is only for parts under construction and development.
        UnderConstructionPolicy::class => UnderConstructionPolicy::class,
        WhenAdminPanelIsInactivePolicy::class => WhenAdminPanelIsInactivePolicy::class,
        WhenCommunityIsInactivePolicy::class => WhenCommunityIsInactivePolicy::class,
        GlobalViewClientEmailPolicy::class => GlobalViewClientEmailPolicy::class,
        GlobalViewClientPhonePolicy::class => GlobalViewClientPhonePolicy::class,

        // USERS MANAGEMENT
        Personnel::class => PersonnelPolicy::class,
        PersonnelRole::class => PersonnelRolePolicy::class,
        PersonnelPermissionRole::class => PersonnelPermissionRolePolicy::class,

        UserBetconstruct::class => UserBetconstructPolicy::class,
        ClientCategory::class => ClientCategoryPolicy::class,
        ClientPermissionCategory::class => ClientPermissionCategoryPolicy::class,
        ClientCategoryMap::class => ClientCategoryMapPolicy::class,
        ClientTrustScore::class => ClientTrustScorePolicy::class,

        // CONTENT MANAGEMENT
        PostCategory::class => PostCategoryPolicy::class,
        PostSpace::class => PostSpacePolicy::class,
        PostSpacePermission::class => PostSpacePermissionPolicy::class,
        PostGroupsDisplayPosition::class => PostGroupsDisplayPositionPolicy::class,

        ArticlePost::class => ArticlePostPolicy::class,
        FaqPost::class => FaqPostPolicy::class,
        PinnedPost::class => PinnedPostPolicy::class,

        Comment::class => CommentPolicy::class,
        UnapprovedComment::class => UnapprovedCommentPolicy::class,

        // SUPPORT
        Chatbot::class => ChatbotPolicy::class,
        ChatbotTester::class => ChatbotTesterPolicy::class,

        Ticket::class => TicketPolicy::class,
        OpenTicket::class => OpenTicketPolicy::class,

        // PROMOTIONAL
        ReferralRewardPackage::class => ReferralRewardPackagePolicy::class,
        ReferralRewardItem::class => ReferralRewardItemPolicy::class,
        ReferralSession::class => ReferralSessionPolicy::class,
        ReferralCustomSetting::class => ReferralCustomSettingPolicy::class,
        Referral::class => ReferralPolicy::class,
        ReferralRewardPayment::class => ReferralRewardPaymentPolicy::class,

        // ASSETS
        DomainExtension::class => DomainExtensionPolicy::class,
        DomainHolder::class => DomainHolderPolicy::class,
        DomainHolderAccount::class => DomainHolderAccountPolicy::class,
        DomainCategory::class => DomainCategoryPolicy::class,
        Domain::class => DomainPolicy::class,
        DomainImport::class => DomainImportPolicy::class,
        DomainGenerator::class => DomainGeneratorPolicy::class,
        DomainPreparingReview::class => DomainPreparingReviewPolicy::class,
        AssignedDomain::class => AssignedDomainPolicy::class,
        AssignedDomainsStatistic::class => AssignedDomainsStatisticPolicy::class,
        ReportedDomain::class => ReportedDomainPolicy::class,
        DedicatedDomain::class => DedicatedDomainPolicy::class,

        // FINANCIAL
        CurrencyRate::class => CurrencyRatePolicy::class,

        // SETTINGS AND SEURITY
        Permission::class => PermissionPolicy::class,
        Setting::class =>  SettingPolicy::class,
        TechnicalSetting::class =>  TechnicalSettingPolicy::class,
        DynamicData::class =>  DynamicDataPolicy::class,
        SystemLogsPolicy::class =>  SystemLogsPolicy::class,

        /*--------------------- AdminPanel Policies END ---------------------*/

        /*--------------------- Site Policies ---------------------*/
        MyTicket::class =>  MyTicketPolicy::class,

        MyChatbotChat::class =>  MyChatbotChatPolicy::class,

        ReferralPanel::class =>  ReferralPanelPolicy::class,
        /*--------------------- Site Policies END ---------------------*/

        /******************* HHHE END ************************/
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        /******************* HHHE ************************/

        /*--------------------- AdminPanel Gates ---------------------*/

        /******************* USERS MANAGEMENT *******************/

        Gate::define('viewAny-PersonnelManagement', function ($user) {

            if ($user->can('viewAny', Personnel::class)) return true;
            if ($user->can('viewAny', PersonnelRole::class)) return true;
            if ($user->can('viewAny', PersonnelPermissionRole::class)) return true;
        });

        Gate::define('viewAny-ClientsManagement', function ($user) {

            if ($user->can('viewAny', UserBetconstruct::class)) return true;
            if ($user->can('viewAny', ClientCategory::class)) return true;
            if ($user->can('viewAny', ClientPermissionCategory::class)) return true;
            if ($user->can('viewAny', ClientCategoryMap::class)) return true;
            if ($user->can('viewAny', ClientTrustScore::class)) return true;
        });

        // Category
        Gate::define('viewAny_Category-UserManagement', function ($user) {

            if ($user->can('viewAny-PersonnelManagement')) return true;
            if ($user->can('viewAny-ClientsManagement')) return true;
        });
        /******************* USERS MANAGEMENT END *******************/

        /******************* CONTENT MANAGEMENT *******************/

        Gate::define('viewAny-PostGrouping', function ($user) {

            if ($user->can('viewAny', PostCategory::class)) return true;
            if ($user->can('viewAny', PostSpace::class)) return true;
            if ($user->can('viewAny', PostSpacePermission::class)) return true;
            if ($user->can('viewAny', PostGroupsDisplayPosition::class)) return true;
        });

        Gate::define('viewAny-Posts', function ($user) {

            if ($user->can('viewAny', ArticlePost::class)) return true;
            if ($user->can('viewAny', FaqPost::class)) return true;
            if ($user->can('viewAny', PinnedPost::class)) return true;
        });

        Gate::define('viewAny-Comments', function ($user) {

            if ($user->can('viewAny', Comment::class)) return true;
            if ($user->can('viewAny', UnapprovedComment::class)) return true;
        });

        // Category
        Gate::define('viewAny_Category-PostManagement', function ($user) {

            if ($user->can('viewAny-PostGrouping')) return true;
            if ($user->can('viewAny-Posts')) return true;
            if ($user->can('viewAny-Comments')) return true;
        });
        /******************* CONTENT MANAGEMENT END *******************/

        /******************* SUPPORT *******************/

        Gate::define('viewAny-Chatbots', function ($user) {

            if ($user->can('viewAny', Chatbot::class)) return true;
            if ($user->can('viewAny', ChatbotTester::class)) return true;
        });

        Gate::define('viewAny-Tickets', function ($user) {

            if ($user->can('viewAny', Ticket::class)) return true;
            if ($user->can('viewAny', OpenTicket::class)) return true;
        });

        Gate::define('answer-Tickets', function ($user) {

            if ($user->can('update', Ticket::class)) return true;
            if ($user->can('update', OpenTicket::class)) return true;
        });

        // Category
        Gate::define('viewAny_Category-Support', function ($user) {

            if ($user->can('viewAny-Chatbots')) return true;
            if ($user->can('viewAny-Tickets')) return true;
        });
        /******************* SUPPORT END *******************/

        /******************* PROMOTIONAL *******************/

        Gate::define('viewAny-Referral', function ($user) {

            if ($user->can('viewAny', ReferralRewardPackage::class)) return true;
            if ($user->can('viewAny', ReferralRewardItem::class)) return true;
            if ($user->can('viewAny', ReferralSession::class)) return true;
            if ($user->can('viewAny', ReferralCustomSetting::class)) return true;
            if ($user->can('viewAny', Referral::class)) return true;
            if ($user->can('viewAny', ReferralRewardPayment::class)) return true;
        });

        // Category
        Gate::define('viewAny_Category-Promotional', function ($user) {

            if ($user->can('viewAny-Referral')) return true;
        });
        /******************* PROMOTIONAL END *******************/

        /******************* ASSETS *******************/

        Gate::define('viewAny-Domains', function ($user) {

            if ($user->can('viewAny', DomainExtension::class)) return true;
            if ($user->can('viewAny', DomainHolder::class)) return true;
            if ($user->can('viewAny', DomainHolderAccount::class)) return true;
            if ($user->can('viewAny', DomainCategory::class)) return true;
            if ($user->can('viewAny', Domain::class)) return true;
            if ($user->can('viewAny', DomainImport::class)) return true;
            if ($user->can('viewAny', DomainGenerator::class)) return true;
            if ($user->can('viewAny', DomainPreparingReview::class)) return true;
            if ($user->can('viewAny', AssignedDomain::class)) return true;
            if ($user->can('viewAny', AssignedDomainsStatistic::class)) return true;
            if ($user->can('viewAny', ReportedDomain::class)) return true;
            if ($user->can('viewAny', DedicatedDomain::class)) return true;
        });

        // Category
        Gate::define('viewAny_Category-Assets', function ($user) {

            if ($user->can('viewAny-Domains')) return true;
        });
        /******************* ASSETS END *******************/

        /******************* FINANCIAL *******************/

        Gate::define('viewAny-Currencies', function ($user) {

            if ($user->can('viewAny', CurrencyRate::class)) return true;
        });

        // Category
        Gate::define('viewAny_Category-Financial', function ($user) {

            if ($user->can('viewAny-Currencies')) return true;
        });
        /******************* FINANCIAL END *******************/

        /******************* SETTINGS AND SEURITY *******************/

        Gate::define('viewAny-AccessControl', function ($user) {

            if ($user->can('viewAny', Permission::class)) return true;
        });

        Gate::define('viewAny-Settings', function ($user) {

            if ($user->can('update', Setting::class)) return true;
            if ($user->can('update', TechnicalSetting::class)) return true;
            if ($user->can('viewAny', DynamicData::class)) return true;
        });

        Gate::define('viewAny-SystemReports', function ($user) {

            if ($user->can('viewAny', SystemLogsPolicy::class)) return true;
        });

        /** LogViewer gates */
        Gate::define('viewLogViewer', function ($user) {
            if ($user->can('viewAny', SystemLogsPolicy::class)) return true;
        });

        Gate::define('downloadLogFile', function ($user, LogFile $file) {
            if ($user->can('view', SystemLogsPolicy::class)) return true;
        });

        Gate::define('downloadLogFolder', function ($user, LogFolder $folder) {
            if ($user->can('view', SystemLogsPolicy::class)) return true;
        });

        Gate::define('deleteLogFile', function ($user, LogFile $file) {
            if ($user->can('delete', SystemLogsPolicy::class)) return true;
        });

        Gate::define('deleteLogFolder', function ($user, LogFolder $folder) {
            if ($user->can('delete', SystemLogsPolicy::class)) return true;
        });

        /** LogViewer gates END */

        // Category
        Gate::define('viewAny_Category-SettingsAndSecurity', function ($user) {

            if ($user->can('viewAny-AccessControl')) return true;
            if ($user->can('viewAny-Settings')) return true;
            if ($user->can('viewAny-SystemReports')) return true;
        });

        /******************* SETTINGS AND SEURITY *******************/

        /*--------------------- AdminPanel Gates END ---------------------*/

        /*--------------------- Sites Gates ---------------------*/


        Gate::define('viewAny_Site-Chatbot', function ($user) {

            if ($user->can('viewAny', MyChatbotChat::class)) return true;
        });

        /*--------------------- Sites Gates END ---------------------*/

        /******************* HHHE END ************************/
    }
}
