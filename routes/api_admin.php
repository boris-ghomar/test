<?php

use App\Http\Controllers\BackOffice\AccessControl\PermissionController;
use App\Http\Controllers\BackOffice\Chatbot\ChatbotController;
use App\Http\Controllers\BackOffice\Chatbot\ChatbotCreatorController;
use App\Http\Controllers\BackOffice\Chatbot\ChatbotTesterController;
use App\Http\Controllers\BackOffice\ClientsManagement\ClientCategoryController;
use App\Http\Controllers\BackOffice\ClientsManagement\ClientPermissionCategoryController;
use App\Http\Controllers\BackOffice\ClientsManagement\UserBetconstructController;
use App\Http\Controllers\BackOffice\Comments\CommentController;
use App\Http\Controllers\BackOffice\PeronnelManagement\PersonnelController;
use App\Http\Controllers\BackOffice\PeronnelManagement\PersonnelPermissionRoleController;
use App\Http\Controllers\BackOffice\PeronnelManagement\PersonnelRoleController;
use App\Http\Controllers\BackOffice\PostGrouping\PostCategoryController;
use App\Http\Controllers\BackOffice\PostGrouping\PostGroupsDisplayPositionController;
use App\Http\Controllers\BackOffice\PostGrouping\PostSpaceController;
use App\Http\Controllers\BackOffice\PostGrouping\PostSpacePermissionController;
use App\Http\Controllers\BackOffice\Posts\ArticlePostController;
use App\Http\Controllers\BackOffice\Posts\FaqPostController;
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
use App\Http\Controllers\BackOffice\Tickets\OpenTicketController;
use App\Http\Controllers\BackOffice\Tickets\TicketController;
use App\Http\Controllers\BackOffice\Tickets\TicketMessengerController;
use App\Http\Controllers\General\NotificationController;
use App\Http\Middleware\HHH\api\checkPaginationData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes defined in the routes/api.php file are nested within a route group by the RouteServiceProvider.
| Within this group, the "/api" URI prefix is automatically applied
| so you do not need to manually apply it to every route in the file.
| You may modify the prefix and other route group options by modifying your RouteServiceProvider class.
| Source: https://laravel.com/docs/8.x/routing
| Example:
| Your route start with: http://YOUR_DOMAIN/api/
*/

/*
    https://laravel.com/docs/8.x/routing#fallback-routes
*/

Route::fallback(function () {

    return response()->json([

        'status'    =>  'failed',
        'error'     => 'general.notFound',
        'message'   => trans('general.NotFound'),

    ], 404);
});


Route::prefix('javascript')->middleware(['auth:sanctum', 'BackOfficeUser', 'IsAdminPanelActive'])->group(function () {

    // GENERAL
    Route::prefix('notifications')->group(function () {

        Route::prefix('inbox')->group(function () {
            //  http://community.cod/api/admin/javascript/notifications/inbox

            Route::post("/", [NotificationController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::delete('/delete',  [NotificationController::class, 'destroy']);
        });
    });

    // USERS MANAGEMENT
    Route::prefix('personnel_management')->group(function () {

        Route::prefix('personnel')->group(function () {
            //  http://community.cod/api/admin/javascript/personnel_management/personnel

            Route::post("/", [PersonnelController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [PersonnelController::class, 'store']);
            Route::put('/update', [PersonnelController::class, 'update']);
            Route::delete('/delete',  [PersonnelController::class, 'destroy']);
        });

        Route::prefix('roles')->group(function () {
            //  http://community.cod/api/admin/javascript/personnel_management/roles

            Route::post("/", [PersonnelRoleController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [PersonnelRoleController::class, 'store']);
            Route::put('/update', [PersonnelRoleController::class, 'update']);
            Route::delete('/delete',  [PersonnelRoleController::class, 'destroy']);
        });

        Route::prefix('role-permissions')->group(function () {
            //  http://community.cod/api/admin/javascript/personnel_management/role-permissions

            Route::post("/", [PersonnelPermissionRoleController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::put('/update', [PersonnelPermissionRoleController::class, 'update']);
        });
    });

    Route::prefix('clients_management')->group(function () {

        Route::prefix('betconstruct_clients')->group(function () {
            //  http://community.cod/api/admin/javascript/clients_management/betconstruct_clients

            Route::post("/", [UserBetconstructController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [UserBetconstructController::class, 'store']);
            Route::put('/update', [UserBetconstructController::class, 'update']);
            Route::delete('/delete',  [UserBetconstructController::class, 'destroy']);
            Route::post('/update_customize_table_settings',  [UserBetconstructController::class, 'updateCustomizeTableSettings']);
            Route::post('/export_excel',  [UserBetconstructController::class, 'exportExcel']);
        });

        Route::prefix('categories')->group(function () {
            //  http://community.cod/api/admin/javascript/clients_management/categories

            Route::post("/", [ClientCategoryController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [ClientCategoryController::class, 'store']);
            Route::put('/update', [ClientCategoryController::class, 'update']);
            Route::delete('/delete',  [ClientCategoryController::class, 'destroy']);
        });

        Route::prefix('category-permissions')->group(function () {
            //  http://community.cod/api/admin/javascript/clients_management/category-permissions

            Route::post("/", [ClientPermissionCategoryController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::put('/update', [ClientPermissionCategoryController::class, 'update']);
        });

        Route::prefix('category_maps')->group(function () {
            //  http://community.cod/api/admin/javascript/clients_management/category_maps

            Route::post("/", [ClientCategoryMapController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [ClientCategoryMapController::class, 'store']);
            Route::put('/update', [ClientCategoryMapController::class, 'update']);
            Route::delete('/delete',  [ClientCategoryMapController::class, 'destroy']);
        });

        Route::prefix('trust_scores')->group(function () {
            //  http://community.cod/api/admin/javascript/clients_management/trust_scores

            Route::post("/", [ClientTrustScoreController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::put('/update', [ClientTrustScoreController::class, 'update']);
            Route::post('/export_excel',  [ClientTrustScoreController::class, 'exportExcel']);
        });
    });

    // CONTENT MANAGEMENT
    Route::prefix('post_grouping')->group(function () {

        Route::prefix('categories')->group(function () {
            //  http://community.cod/api/admin/javascript/post_grouping/categories

            Route::post("/", [PostCategoryController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [PostCategoryController::class, 'store']);
            Route::put('/update', [PostCategoryController::class, 'update']);
            Route::delete('/delete',  [PostCategoryController::class, 'destroy']);
        });

        Route::prefix('spaces')->group(function () {
            //  http://community.cod/api/admin/javascript/post_grouping/spaces

            Route::post("/", [PostSpaceController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [PostSpaceController::class, 'store']);
            Route::put('/update', [PostSpaceController::class, 'update']);
            Route::delete('/delete',  [PostSpaceController::class, 'destroy']);
        });

        Route::prefix('post_space_permission')->group(function () {
            //  http://community.cod/api/admin/javascript/post_grouping/post_space_permission

            Route::post("/", [PostSpacePermissionController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::put('/update', [PostSpacePermissionController::class, 'update']);
        });

        Route::prefix('post_groups_display_position')->group(function () {
            //  http://community.cod/api/admin/javascript/post_grouping/post_groups_display_position

            Route::post("/", [PostGroupsDisplayPositionController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::put('/update', [PostGroupsDisplayPositionController::class, 'update']);
        });
    });

    Route::prefix('posts')->group(function () {

        Route::prefix('article')->group(function () {
            //  http://community.cod/api/admin/javascript/posts/article

            Route::post("/", [ArticlePostController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [ArticlePostController::class, 'store']);
            Route::put('/update', [ArticlePostController::class, 'update']);
            Route::delete('/delete',  [ArticlePostController::class, 'destroy']);
        });

        Route::prefix('faq')->group(function () {
            //  http://community.cod/api/admin/javascript/posts/faq

            Route::post("/", [FaqPostController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [FaqPostController::class, 'store']);
            Route::put('/update', [FaqPostController::class, 'update']);
            Route::delete('/delete',  [FaqPostController::class, 'destroy']);
        });

        Route::prefix('pinned')->group(function () {
            //  http://community.cod/api/admin/javascript/posts/pinned

            Route::post("/", [PinnedPostController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [PinnedPostController::class, 'store']);
            Route::put('/update', [PinnedPostController::class, 'update']);
            Route::delete('/delete',  [PinnedPostController::class, 'destroy']);
        });
    });

    Route::prefix('comments')->group(function () {

        Route::prefix('management')->group(function () {
            //  http://community.cod/api/admin/javascript/comments/management

            Route::post("/", [CommentController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [CommentController::class, 'store']);
            Route::put('/update', [CommentController::class, 'update']);
            Route::delete('/delete',  [CommentController::class, 'destroy']);
        });

        Route::prefix('unapproved_comments')->group(function () {
            //  http://community.cod/api/admin/javascript/comments/unapproved_comments

            Route::post("/", [UnapprovedCommentController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [UnapprovedCommentController::class, 'store']);
            Route::put('/update', [UnapprovedCommentController::class, 'update']);
            Route::delete('/delete',  [UnapprovedCommentController::class, 'destroy']);
        });
    });

    // SUPPORT
    Route::prefix('chatbots')->group(function () {

        Route::prefix('bots_management')->group(function () {
            //  http://community.cod/api/admin/javascript/chatbots/bots_management

            Route::post("/", [ChatbotController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [ChatbotController::class, 'store']);
            Route::put('/update', [ChatbotController::class, 'update']);
            Route::delete('/delete',  [ChatbotController::class, 'destroy']);
        });

        Route::prefix('testers')->group(function () {
            //  http://community.cod/api/admin/javascript/chatbots/testers

            Route::post("/", [ChatbotTesterController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [ChatbotTesterController::class, 'store']);
            Route::put('/update', [ChatbotTesterController::class, 'update']);
            Route::delete('/delete',  [ChatbotTesterController::class, 'destroy']);
        });

        Route::prefix('creator')->group(function () {
            //  http://community.cod/api/admin/javascript/chatbots/creator

            Route::post("/get_steps_tree", [ChatbotCreatorController::class, 'getStepsTree']);
            Route::post("/add_new_step", [ChatbotCreatorController::class, 'addNewStep']);
            Route::post("/delete_step", [ChatbotCreatorController::class, 'deleteStep']);
            Route::post("/update_step", [ChatbotCreatorController::class, 'updateStep']);
            Route::post("/move_step", [ChatbotCreatorController::class, 'moveStep']);
        });
    });

    Route::prefix('tickets')->group(function () {

        Route::prefix('all_tickets')->group(function () {
            //  http://community.cod/api/admin/javascript/tickets/all_tickets

            Route::post("/", [TicketController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [TicketController::class, 'store']);
            Route::put('/update', [TicketController::class, 'update']);
            Route::delete('/delete',  [TicketController::class, 'destroy']);
        });

        Route::prefix('open_tickets')->group(function () {
            //  http://community.cod/api/admin/javascript/tickets/open_tickets

            Route::post("/", [OpenTicketController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [OpenTicketController::class, 'store']);
            Route::put('/update', [OpenTicketController::class, 'update']);
            Route::delete('/delete',  [OpenTicketController::class, 'destroy']);
        });

        Route::prefix('messenger')->withoutMiddleware('throttle:api')->group(function () {
            //  http://community.cod/api/admin/javascript/tickets/messenger

            Route::post('/get_initial_data', [TicketMessengerController::class, 'getInitialData']);
            Route::post('/get_previous_messages', [TicketMessengerController::class, 'getPreviousMessages']);
            Route::post('/get_profile_data', [TicketMessengerController::class, 'getProfileData']);
            Route::post('/new_message', [TicketMessengerController::class, 'newMessage']);
            Route::post('/change_ticket_status', [TicketMessengerController::class, 'changeTicketStatus']);
            Route::post('/submit_form', [TicketMessengerController::class, 'submitForm']);
            Route::post("log", [TicketMessengerController::class, 'registerLog']);
        });
    });

    // PROMOTIONAL
    Route::prefix('referral')->group(function () {

        Route::prefix('referral_reward_packages')->group(function () {
            //  http://community.cod/api/admin/javascript/referral/referral_reward_packages

            Route::post("/", [ReferralRewardPackageController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [ReferralRewardPackageController::class, 'store']);
            Route::put('/update', [ReferralRewardPackageController::class, 'update']);
            Route::delete('/delete',  [ReferralRewardPackageController::class, 'destroy']);
        });

        Route::prefix('referral_reward_items')->group(function () {
            //  http://community.cod/api/admin/javascript/referral/referral_reward_items

            Route::post("/", [ReferralRewardItemController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [ReferralRewardItemController::class, 'store']);
            Route::put('/update', [ReferralRewardItemController::class, 'update']);
            Route::delete('/delete',  [ReferralRewardItemController::class, 'destroy']);
        });

        Route::prefix('referral_sessions')->group(function () {
            //  http://community.cod/api/admin/javascript/referral/referral_sessions

            Route::post("/", [ReferralSessionController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [ReferralSessionController::class, 'store']);
            Route::put('/update', [ReferralSessionController::class, 'update']);
            Route::delete('/delete',  [ReferralSessionController::class, 'destroy']);
        });

        Route::prefix('client_custom_settings')->group(function () {
            //  http://community.cod/api/admin/javascript/referral/client_custom_settings

            Route::post("/", [ReferralCustomSettingController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [ReferralCustomSettingController::class, 'store']);
            Route::put('/update', [ReferralCustomSettingController::class, 'update']);
            Route::delete('/delete',  [ReferralCustomSettingController::class, 'destroy']);
        });

        Route::prefix('referrals_management')->group(function () {
            //  http://community.cod/api/admin/javascript/referral/referrals_management

            Route::post("/", [ReferralController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::put('/update', [ReferralController::class, 'update']);
            Route::post('/export_excel',  [ReferralController::class, 'exportExcel']);
        });

        Route::prefix('reward_payment')->group(function () {
            //  http://community.cod/api/admin/javascript/referral/reward_payment

            Route::post("/", [ReferralRewardPaymentController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::put('/update', [ReferralRewardPaymentController::class, 'update']);
            Route::post('/export_excel',  [ReferralRewardPaymentController::class, 'exportExcel']);
        });
    });

    // ASSETS
    Route::prefix('domains')->group(function () {

        Route::prefix('extensions')->group(function () {
            //  http://community.cod/api/admin/javascript/domains/extensions

            Route::post("/", [DomainExtensionController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [DomainExtensionController::class, 'store']);
            Route::put('/update', [DomainExtensionController::class, 'update']);
            Route::delete('/delete',  [DomainExtensionController::class, 'destroy']);
        });

        Route::prefix('holders')->group(function () {
            //  http://community.cod/api/admin/javascript/domains/holders

            Route::post("/", [DomainHolderController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [DomainHolderController::class, 'store']);
            Route::put('/update', [DomainHolderController::class, 'update']);
            Route::delete('/delete',  [DomainHolderController::class, 'destroy']);
        });

        Route::prefix('holders_accounts')->group(function () {
            //  http://community.cod/api/admin/javascript/domains/holders_accounts

            Route::post("/", [DomainHolderAccountController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [DomainHolderAccountController::class, 'store']);
            Route::put('/update', [DomainHolderAccountController::class, 'update']);
            Route::delete('/delete',  [DomainHolderAccountController::class, 'destroy']);
            Route::post('/export_excel',  [DomainHolderAccountController::class, 'exportExcel']);
        });

        Route::prefix('categories')->group(function () {
            //  http://community.cod/api/admin/javascript/domains/categories

            Route::post("/", [DomainCategoryController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [DomainCategoryController::class, 'store']);
            Route::put('/update', [DomainCategoryController::class, 'update']);
            Route::delete('/delete',  [DomainCategoryController::class, 'destroy']);
        });

        Route::prefix('all')->group(function () {
            //  http://community.cod/api/admin/javascript/domains/all

            Route::post("/", [DomainController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [DomainController::class, 'store']);
            Route::put('/update', [DomainController::class, 'update']);
            Route::delete('/delete',  [DomainController::class, 'destroy']);
            Route::post('/export_excel',  [DomainController::class, 'exportExcel']);
        });

        Route::prefix('import_domains')->group(function () {
            //  http://community.cod/api/admin/javascript/domains/import_domains

            Route::post('/import', [DomainImportController::class, 'import']);
        });

        Route::prefix('preparing_review')->group(function () {
            //  http://community.cod/api/admin/javascript/domains/preparing_review

            Route::post('/get_domain_for_review', [DomainPreparingReviewController::class, 'getDomainForReview']);
            Route::post('/submit_review', [DomainPreparingReviewController::class, 'submitReview']);
        });

        Route::prefix('assigned_domains')->group(function () {
            //  http://community.cod/api/admin/javascript/domains/assigned_domains

            Route::post("/", [AssignedDomainController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/export_excel',  [AssignedDomainController::class, 'exportExcel']);
        });

        Route::prefix('assigned_domains_statistics')->group(function () {
            //  http://community.cod/api/admin/javascript/domains/assigned_domains_statistics

            Route::post("/", [AssignedDomainsStatisticController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/export_excel',  [AssignedDomainsStatisticController::class, 'exportExcel']);
        });

        Route::prefix('reported_domains')->group(function () {
            //  http://community.cod/api/admin/javascript/domains/reported_domains

            Route::post("/", [ReportedDomainController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [ReportedDomainController::class, 'store']);
            Route::put('/update', [ReportedDomainController::class, 'update']);
            Route::delete('/delete',  [ReportedDomainController::class, 'destroy']);
            Route::post('/export_excel',  [ReportedDomainController::class, 'exportExcel']);
        });

        Route::prefix('dedicated_domains')->group(function () {
            //  http://community.cod/api/admin/javascript/domains/dedicated_domains

            Route::post("/", [DedicatedDomainController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [DedicatedDomainController::class, 'store']);
            Route::put('/update', [DedicatedDomainController::class, 'update']);
            Route::delete('/delete',  [DedicatedDomainController::class, 'destroy']);
            Route::post('/export_excel',  [DedicatedDomainController::class, 'exportExcel']);
        });
    });

    // FINANCIAL
    Route::prefix('currencies')->group(function () {

        Route::prefix('currency_rates')->group(function () {
            //  http://community.cod/api/admin/javascript/currencies/currency_rates

            Route::post("/", [CurrencyRateController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::post('/insert', [CurrencyRateController::class, 'store']);
            Route::put('/update', [CurrencyRateController::class, 'update']);
            Route::delete('/delete',  [CurrencyRateController::class, 'destroy']);
        });
    });

    // SETTINGS AND SEURITY
    Route::prefix('access_control')->group(function () {

        Route::prefix('permissions')->group(function () {
            //  http://community.cod/api/admin/javascript/access_control/permissions

            Route::post("/", [PermissionController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::put('/update', [PermissionController::class, 'update']);
        });
    });

    Route::prefix('settings')->group(function () {

        Route::prefix('dynamic_data')->group(function () {
            //  http://community.cod/api/admin/javascript/settings/dynamic_data

            Route::post("/", [DynamicDataController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::put('/update', [DynamicDataController::class, 'update']);
        });
    });


    /******************* Only back office users (Personnel) END *******************/
});
