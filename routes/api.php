<?php

use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\SwarmApiController;
use App\Http\Controllers\General\NotificationController;
use App\Http\Controllers\Site\Chatbot\ChatbotMessengerController;
use App\Http\Controllers\Site\Auth\Betconstruct\LoginBetconstructWebSocketController;
use App\Http\Controllers\Site\Auth\Betconstruct\RegisterBetconstructController;
use App\Http\Controllers\Site\DashboardController;
use App\Http\Controllers\Site\Tickets\ClientTicketMessengerController;
use App\Http\Controllers\Site\UserActions\CommentController;
use App\Http\Controllers\Site\UserActions\LikeController;
use App\Http\Controllers\Site\UserBetconstructProfileController;
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

// Without auth
Route::prefix('javascript')->middleware(['IsCommunityActive'])->group(function () {

    Route::prefix('registration')->group(function () {

        //  http://community.cod/api/javascript/registration/get_cities
        Route::post("get_cities", [RegisterBetconstructController::class, 'apiGetCities']);

        //  http://community.cod/api/javascript/registration/send_email_verification
        // Route::post("send_email_verification", [UserBetconstructProfileController::class, 'apiSendEmailVerification']);
    });
});

Route::prefix('javascript')->middleware(['auth:sanctum', 'IsCommunityActive'])->group(function () {

    Route::prefix('user_actions')->withoutMiddleware('auth:sanctum')->group(function () {

        //  http://community.cod/api/javascript/user_actions/like
        Route::post("like", [LikeController::class, 'action'])->middleware('throttle:15,1');

        //  http://community.cod/api/javascript/user_actions/comment
        Route::post("comment", [CommentController::class, 'action'])->middleware('throttle:5,1');
    });

    Route::prefix('bcswarm')->withoutMiddleware('auth:sanctum')->group(function () {

        //  http://community.cod/api/javascript/bcswarm/get_initial_data
        Route::post("get_initial_data", [SwarmApiController::class, 'getInitialData']);

        //  http://community.cod/api/javascript/bcswarm/get_initial_data
        Route::post("log", [SwarmApiController::class, 'registerLog']);

        //  http://community.cod/api/javascript/bcswarm/get_initial_data
        Route::post("get_error_message", [SwarmApiController::class, 'getErrorMessage']);
    });

    Route::prefix('bclogin')->withoutMiddleware('auth:sanctum')->group(function () {

        //  http://community.cod/api/javascript/bclogin/get_initial_data
        Route::post("get_initial_data", [LoginBetconstructWebSocketController::class, 'getInitialData']);

        //  http://community.cod/api/javascript/bclogin/attempt
        Route::post("attempt", [LoginBetconstructWebSocketController::class, 'attempt'])->middleware('throttle:5,1');
    });

    Route::prefix('profile')->group(function () {

        //  http://community.cod/api/javascript/profile/get_cities
        Route::post("get_cities", [UserBetconstructProfileController::class, 'apiGetCities']);

        //  http://community.cod/api/javascript/profile/send_email_verification
        Route::post("send_email_verification", [UserBetconstructProfileController::class, 'apiSendEmailVerification']);
    });

    Route::prefix('chatbot')->group(function () {

        Route::prefix('messenger')->withoutMiddleware('auth:sanctum')->group(function () {
            //  http://community.cod/api/javascript/chatbot/messenger

            Route::post('/get_initial_data', [ChatbotMessengerController::class, 'getInitialData']);
            Route::post('/get_previous_messages', [ChatbotMessengerController::class, 'getPreviousMessages']);
            Route::post('/get_next_step_message', [ChatbotMessengerController::class, 'getNextStepMessage']);
            Route::post('/go_to_step', [ChatbotMessengerController::class, 'goToStep']);
            Route::post('/submit_user_input', [ChatbotMessengerController::class, 'submitUserInput']);
            Route::post('/close_chat', [ChatbotMessengerController::class, 'closeChat']);
        })->middleware('throttle:150,1');
    });

    Route::prefix('notifications')->group(function () {

        Route::prefix('inbox')->group(function () {
            //  http://community.cod/api/javascript/notifications/inbox

            Route::post("/", [NotificationController::class, 'apiIndex'])
                ->middleware(checkPaginationData::class);

            Route::delete('/delete',  [NotificationController::class, 'destroy']);
        });
    });

    Route::prefix('tickets')->group(function () {

        Route::prefix('messenger')->withoutMiddleware('throttle:api')->group(function () {
            //  http://community.cod/api/javascript/tickets/messenger

            Route::post('/get_initial_data', [ClientTicketMessengerController::class, 'getInitialData']);
            Route::post('/get_previous_messages', [ClientTicketMessengerController::class, 'getPreviousMessages']);
            Route::post('/get_profile_data', [ClientTicketMessengerController::class, 'getProfileData']);
            Route::post('/new_message', [ClientTicketMessengerController::class, 'newMessage']);
            Route::post("log", [ClientTicketMessengerController::class, 'registerLog']);
        });
    });

    Route::prefix('dashboard')->group(function () {

        Route::prefix('domain')->group(function () {
            //  http://community.cod/api/javascript/dashboard/domain

            Route::post('/report_domain_issue', [DashboardController::class, 'apiReportDomainIssue']);
        });
    });
});
