<?php

use App\Enums\Routes\AdminRoutesEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Enums\Session\LocaleKeyEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\Export\DownloadExportedFile;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\Http\Controllers\BackOffice\DashboardController;
use App\Http\Controllers\BackOffice\AccessControl\PermissionController;
use App\Http\Controllers\BackOffice\Auth\ForgotPasswordController;
use App\Http\Controllers\BackOffice\Auth\LoginController;
use App\Http\Controllers\BackOffice\Auth\PasswordResetController;
use App\Http\Controllers\BackOffice\Chatbot\ChatbotController;
use App\Http\Controllers\BackOffice\Chatbot\ChatbotTesterController;
use App\Http\Controllers\BackOffice\ClientsManagement\ClientCategoryController;
use App\Http\Controllers\BackOffice\ClientsManagement\ClientPermissionCategoryController;
use App\Http\Controllers\BackOffice\ClientsManagement\UserBetconstructController;
use App\Http\Controllers\BackOffice\Comments\CommentController;
use App\Http\Controllers\BackOffice\PeronnelManagement\PersonnelController;
use App\Http\Controllers\BackOffice\PeronnelManagement\PersonnelPermissionRoleController;
use App\Http\Controllers\BackOffice\PeronnelManagement\PersonnelRoleController;
use App\Http\Controllers\BackOffice\PersonnelProfileController;
use App\Http\Controllers\BackOffice\PostGrouping\PostCategoryController;
use App\Http\Controllers\BackOffice\PostGrouping\PostGroupsDisplayPositionController;
use App\Http\Controllers\BackOffice\PostGrouping\PostSpaceController;
use App\Http\Controllers\BackOffice\PostGrouping\PostSpacePermissionController;
use App\Http\Controllers\BackOffice\Posts\ArticlePostController;
use App\Http\Controllers\BackOffice\Posts\FaqPostController;
use App\Http\Controllers\BackOffice\Settings\SettingController;
use App\Http\Controllers\BackOffice\ClientsManagement\ClientCategoryMapController;
use App\Http\Controllers\BackOffice\ClientsManagement\ClientTrustScoreController;
use App\Http\Controllers\BackOffice\Comments\UnapprovedCommentController;
use App\Http\Controllers\BackOffice\Currencies\CurrencyRateController;
use App\Http\Controllers\BackOffice\Domains\AssignedDomainController;
use App\Http\Controllers\BackOffice\Domains\AssignedDomainsStatisticController;
use App\Http\Controllers\BackOffice\Domains\DedicatedDomainController;
use App\Http\Controllers\BackOffice\Domains\DomainCategoryController;
use App\Http\Controllers\BackOffice\Domains\DomainController;
use App\Http\Controllers\BackOffice\Domains\DomainExtensionController;
use App\Http\Controllers\BackOffice\Domains\DomainGeneratorController;
use App\Http\Controllers\BackOffice\Domains\DomainHolderAccountController;
use App\Http\Controllers\BackOffice\Domains\DomainHolderController;
use App\Http\Controllers\BackOffice\Domains\DomainImportController;
use App\Http\Controllers\BackOffice\Domains\DomainPreparingReviewController;
use App\Http\Controllers\BackOffice\Domains\ReportedDomainController;
use App\Http\Controllers\BackOffice\Posts\PinnedPostController;
use App\Http\Controllers\BackOffice\Referral\ReferralController;
use App\Http\Controllers\BackOffice\Referral\ReferralCustomSettingController;
use App\Http\Controllers\BackOffice\Referral\ReferralRewardItemController;
use App\Http\Controllers\BackOffice\Referral\ReferralRewardPackageController;
use App\Http\Controllers\BackOffice\Referral\ReferralRewardPaymentController;
use App\Http\Controllers\BackOffice\Referral\ReferralSessionController;
use App\Http\Controllers\BackOffice\Settings\DynamicDataController;
use App\Http\Controllers\BackOffice\Settings\TechnicalSettingController;
use App\Http\Controllers\BackOffice\Tickets\OpenTicketController;
use App\Http\Controllers\BackOffice\Tickets\TicketController;
use App\Http\Controllers\BackOffice\Tickets\TicketMessengerController;
use App\Http\Controllers\General\NotificationController;
use App\Models\General\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes for admin (Back Office) section
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::fallback(function () {
    return abort(404);
});

/**************************************************************/
//Localization
Route::get('locale/{locale}', function ($locale) {

    Session::put(LocaleKeyEnum::AdminPanel->value, $locale);

    if (auth()->check())
        UserSetting::saveItem(AppSettingsEnum::AdminPanelDefaultLanguage, LocaleEnum::getNameByValue($locale));

    return redirect()->back();
})->name(AdminPublicRoutesEnum::Locale->value);
//Localization END
/**************************************************************/


Route::prefix('auth')->middleware('GeustUser')->group(function () {

    Route::prefix('login')->group(function () {

        Route::get('/', [LoginController::class, 'index'])->name(AdminPublicRoutesEnum::Login->value);
        Route::post('/', [LoginController::class, 'attempt'])->middleware('throttle:5,1')->name(AdminPublicRoutesEnum::Login->value);
    });

    Route::prefix('forgot-password')->group(function () {

        Route::get('/', [ForgotPasswordController::class, 'index'])->name(AdminPublicRoutesEnum::ForgotPassword->value);
        Route::post('/', [ForgotPasswordController::class, 'attempt'])->middleware('throttle:3,1')->name(AdminPublicRoutesEnum::ForgotPassword->value);
    });

    Route::prefix('reset-password')->group(function () {

        Route::get('/{token}', [PasswordResetController::class, 'index'])->name(AdminPublicRoutesEnum::ResetPasswordIndex->value);
        Route::post('/', [PasswordResetController::class, 'attempt'])->middleware('throttle:5,1')->name(AdminPublicRoutesEnum::ResetPasswordAttempt->value);
    });
});



Route::middleware(['auth', 'BackOfficeUser', 'IsAdminPanelActive'])->group(function () {

    // GENERAL
    Route::prefix('export')->group(function () {

        Route::get('/excel/{savedFileName}/{downloadFileName}', [DownloadExportedFile::class, 'downloadExcelFile'])->name(AdminPublicRoutesEnum::ExportExcel->value);
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name(AdminPublicRoutesEnum::Dashboard->value);

    Route::prefix('/notifications')->group(function () {

        Route::get('/', [NotificationController::class, 'index'])->name(AdminPublicRoutesEnum::Notifications->value);
        Route::delete('/', [NotificationController::class, 'destroyAll'])->name(AdminPublicRoutesEnum::Notifications->value);
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [PersonnelProfileController::class, 'edit'])->name(AdminPublicRoutesEnum::Profile->value);
        Route::put('/', [PersonnelProfileController::class, 'update'])->name(AdminPublicRoutesEnum::Profile->value);
    });

    // USERS MANAGEMENT
    Route::prefix('personnel')->group(function () {

        Route::get('/personnel-management', [PersonnelController::class, 'index'])->name(AdminRoutesEnum::Personnel_Management->value);
        Route::get('/personnel-roles_management', [PersonnelRoleController::class, 'index'])->name(AdminRoutesEnum::Personnel_RolesManagement->value);
        Route::get('/personnel-roles_permissions', [PersonnelPermissionRoleController::class, 'index'])->name(AdminRoutesEnum::Personnel_RolesPermissions->value);
    });

    Route::prefix('clients')->group(function () {

        Route::get('/betconstruct-clients-management', [UserBetconstructController::class, 'index'])->name(AdminRoutesEnum::BetconstructClients_Management->value);
        Route::get('/clients-categories_management', [ClientCategoryController::class, 'index'])->name(AdminRoutesEnum::Clients_CategoriesManagement->value);
        Route::get('/clients-categories_permissions', [ClientPermissionCategoryController::class, 'index'])->name(AdminRoutesEnum::Clients_CategoriesPermissions->value);
        Route::get('/clients-categories_maps', [ClientCategoryMapController::class, 'index'])->name(AdminRoutesEnum::Clients_CategoriesMaps->value);
        Route::get('/clients-trust-scores', [ClientTrustScoreController::class, 'index'])->name(AdminRoutesEnum::Clients_TrustScores->value);
    });

    // CONTENT MANAGEMENT
    Route::prefix('post_grouping')->group(function () {

        Route::get('/post-categories-management', [PostCategoryController::class, 'index'])->name(AdminRoutesEnum::PostGrouping_Categories->value);
        Route::get('/post-spaces-management', [PostSpaceController::class, 'index'])->name(AdminRoutesEnum::PostGrouping_Spaces->value);
        Route::get('/post-spaces-permissions-management', [PostSpacePermissionController::class, 'index'])->name(AdminRoutesEnum::PostGrouping_PostSpacesPermissions->value);
        Route::get('/post-groups-display-position', [PostGroupsDisplayPositionController::class, 'index'])->name(AdminRoutesEnum::PostGrouping_PostGroupsDisplayPosition->value);
    });

    Route::prefix('posts')->group(function () {

        Route::prefix('articles')->group(function () {

            Route::get('/article-posts-management', [ArticlePostController::class, 'index'])->name(AdminRoutesEnum::Posts_Articles->value);
            Route::get('/article-post-create', [ArticlePostController::class, 'create'])->name(AdminPublicRoutesEnum::Posts_ArticlesCreate->value);
            Route::post('/article-post-create', [ArticlePostController::class, 'store'])->name(AdminPublicRoutesEnum::Posts_ArticlesCreate->value);
            Route::get('/article-post-edit/{articlePost}', [ArticlePostController::class, 'edit'])->name(AdminPublicRoutesEnum::Posts_ArticlesEdit->value);
            Route::put('/article-post-edit/{articlePost}', [ArticlePostController::class, 'update'])->name(AdminPublicRoutesEnum::Posts_ArticlesEdit->value);
        });

        Route::prefix('faq')->group(function () {

            Route::get('/faq-posts-management', [FaqPostController::class, 'index'])->name(AdminRoutesEnum::Posts_Faq->value);
            Route::get('/faq-post-create', [FaqPostController::class, 'create'])->name(AdminPublicRoutesEnum::Posts_FaqCreate->value);
            Route::post('/faq-post-create', [FaqPostController::class, 'store'])->name(AdminPublicRoutesEnum::Posts_FaqCreate->value);
            Route::get('/faq-post-edit/{faqPost}', [FaqPostController::class, 'edit'])->name(AdminPublicRoutesEnum::Posts_FaqEdit->value);
            Route::put('/faq-post-edit/{faqPost}', [FaqPostController::class, 'update'])->name(AdminPublicRoutesEnum::Posts_FaqEdit->value);
        });

        Route::get('/pinned-posts-management', [PinnedPostController::class, 'index'])->name(AdminRoutesEnum::Posts_Pinned->value);
    });

    Route::prefix('comments')->group(function () {

        Route::get('/comments-management', [CommentController::class, 'index'])->name(AdminRoutesEnum::Comments_Management->value);
        Route::get('/unapproved_comments_management', [UnapprovedCommentController::class, 'index'])->name(AdminRoutesEnum::UnapprovedComments_Management->value);
    });

    // SUPPORT
    Route::prefix('chatbots')->group(function () {

        Route::get('/chatbots_management', [ChatbotController::class, 'index'])->name(AdminRoutesEnum::Chatbots_Bots->value);
        Route::get('/edit-chatbot-{chatbot}', [ChatbotController::class, 'edit'])->name(AdminPublicRoutesEnum::Chatbots_EditBot->value);
        Route::get('/chatbot_testers', [ChatbotTesterController::class, 'index'])->name(AdminRoutesEnum::Chatbots_Testers->value);
    });

    Route::prefix('tickets')->group(function () {

        Route::get('/all-tickets', [TicketController::class, 'index'])->name(AdminRoutesEnum::Tickets_AllTickets->value);
        Route::get('/open-tickets', [OpenTicketController::class, 'index'])->name(AdminRoutesEnum::Tickets_OpenTickets->value);
        Route::get('/ticket-answering/ticket-{ticket}', [TicketMessengerController::class, 'index'])->name(AdminPublicRoutesEnum::Ticket_Messenger->value);
    });

    // PROMOTIONAL
    Route::prefix('referral')->group(function () {

        Route::get('/referral-reward-packages', [ReferralRewardPackageController::class, 'index'])->name(AdminRoutesEnum::Referral_RewardPackages->value);
        Route::get('/referral-reward-items', [ReferralRewardItemController::class, 'index'])->name(AdminRoutesEnum::Referral_RewardItems->value);
        Route::get('/referral-sessions', [ReferralSessionController::class, 'index'])->name(AdminRoutesEnum::Referral_ReferralSessions->value);
        Route::get('/client-custom-settings', [ReferralCustomSettingController::class, 'index'])->name(AdminRoutesEnum::Referral_ClientCustomSettings->value);
        Route::get('/referrals-management', [ReferralController::class, 'index'])->name(AdminRoutesEnum::Referral_ReferralsManagement->value);
        Route::get('/reward-payments', [ReferralRewardPaymentController::class, 'index'])->name(AdminRoutesEnum::Referral_RewardPayments->value);
    });

    // ASSETS
    Route::prefix('domains')->group(function () {

        Route::get('/extensions', [DomainExtensionController::class, 'index'])->name(AdminRoutesEnum::Domains_Extensions->value);
        Route::get('/holders-list', [DomainHolderController::class, 'index'])->name(AdminRoutesEnum::Domains_Holders->value);
        Route::get('/holders-accounts', [DomainHolderAccountController::class, 'index'])->name(AdminRoutesEnum::Domains_HolderAccounts->value);
        Route::get('/domain-categories', [DomainCategoryController::class, 'index'])->name(AdminRoutesEnum::Domains_Categories->value);
        Route::get('/domains-list', [DomainController::class, 'index'])->name(AdminRoutesEnum::Domains_All->value);
        Route::get('/domains-import', [DomainImportController::class, 'index'])->name(AdminRoutesEnum::Domains_ImportDomains->value);
        Route::prefix('domains-generator')->group(function () {

            Route::get('/', [DomainGeneratorController::class, 'index'])->name(AdminRoutesEnum::Domains_DomainGenerator->value);
            Route::post('/', [DomainGeneratorController::class, 'generate']);
        });
        Route::get('/domains-preparing-review', [DomainPreparingReviewController::class, 'index'])->name(AdminRoutesEnum::Domains_DomainPreparingReview->value);
        Route::get('/assigned-domains', [AssignedDomainController::class, 'index'])->name(AdminRoutesEnum::Domains_AssignedDomains->value);
        Route::get('/assigned_domains_statistics', [AssignedDomainsStatisticController::class, 'index'])->name(AdminRoutesEnum::Domains_AssignedDomainsStatistics->value);
        Route::get('/reported-domains', [ReportedDomainController::class, 'index'])->name(AdminRoutesEnum::Domains_ReportedDomains->value);
        Route::get('/dedicated-domains', [DedicatedDomainController::class, 'index'])->name(AdminRoutesEnum::Domains_DedicatedDomains->value);
    });

    // FINANCIAL
    Route::prefix('currencies')->group(function () {

        Route::get('/currency_rates', [CurrencyRateController::class, 'index'])->name(AdminRoutesEnum::Currencies_CurrencyRates->value);
    });

    // SETTINGS AND SEURITY
    Route::prefix('access_control')->group(function () {

        Route::get('/permissions_management', [PermissionController::class, 'index'])->name(AdminRoutesEnum::AccessControl_Permissions->value);
    });

    Route::prefix('settings')->group(function () {

        Route::prefix('general-settings')->group(function () {
            Route::get('/', [SettingController::class, 'edit'])->name(AdminRoutesEnum::Settings_GeneralSettings->value);
            Route::put('/', [SettingController::class, 'update'])->name(AdminRoutesEnum::Settings_GeneralSettings->value);
        });

        Route::prefix('technical-settings')->group(function () {
            Route::get('/', [TechnicalSettingController::class, 'edit'])->name(AdminRoutesEnum::Settings_TechnicalSettings->value);
            Route::put('/', [TechnicalSettingController::class, 'update'])->name(AdminRoutesEnum::Settings_TechnicalSettings->value);
        });

        Route::prefix('dynamic-data')->group(function () {
            Route::get('/', [DynamicDataController::class, 'index'])->name(AdminRoutesEnum::Settings_DynamicData->value);
        });
    });
});
