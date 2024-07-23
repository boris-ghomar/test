<?php

use App\Enums\Database\Tables\ReferralsTableEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Routes\SiteRoutesEnum;
use App\Enums\Session\GeneralSessionsEnum;
use App\Enums\Session\LocaleKeyEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\HashHmacHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\SessionEnum as SessionEnumSwarmApi;
use App\Http\Controllers\Site\Auth\Betconstruct\BetconstructSSOController;
use App\Http\Controllers\Site\Auth\Betconstruct\ForgotPasswordController;
use App\Http\Controllers\Site\Auth\Betconstruct\LoginBetconstructApiController;
use App\Http\Controllers\Site\Auth\Betconstruct\LoginBetconstructWebSocketController;
use App\Http\Controllers\Site\Auth\Betconstruct\RegisterBetconstructController;
use App\Http\Controllers\Site\Chatbot\ChatbotMessengerController;
use App\Http\Controllers\Site\DisplayContent\PostGroupContentDisplayController;
use App\Http\Controllers\Site\DisplayContent\PostShowController;
use App\Http\Controllers\Site\SiteMainPageController;
use App\Http\Controllers\Site\SiteSearchController;
use App\Http\Controllers\Site\CustomPages\IpRestrictionController;
use App\Http\Controllers\Site\DashboardController;
use App\Http\Controllers\Site\NotificationController;
use App\Http\Controllers\Site\Referral\ReferralPanelController;
use App\Http\Controllers\Site\Tickets\ClientTicketMessengerController;
use App\Http\Controllers\Site\Tickets\MyTicketController;
use App\Http\Controllers\Site\UserBetconstructProfileController;
use App\Models\BackOffice\Referral\Referral;
use App\Models\General\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
// Laravel Example
Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
*/

/**
 * Virtual Subdomain:
 *
 * In order to ensure your subdomain routes are reachable,
 * you should register subdomain routes before registering root domain routes.
 * This will prevent root domain routes from overwriting subdomain routes which have the same URI path.
 */


Route::fallback(function () {
    return abort(404);
});

/**************************************************************/
//Localization
Route::get('locale/{locale}', function ($locale) {

    Session::put(LocaleKeyEnum::Community->value, $locale);

    if (auth()->check())
        UserSetting::saveItem(AppSettingsEnum::CommunityDefaultLanguage, LocaleEnum::getNameByValue($locale));

    return redirect()->back();
})->name(SitePublicRoutesEnum::Locale->value);
//Localization END
/**************************************************************/

// Shortcut to access admin panel
Route::get('adminpanel', function () {
    return redirect(AdminPublicRoutesEnum::Login->route());
});

Route::prefix('ip_restriction')->group(function () {

    Route::get('/', [IpRestrictionController::class, 'index'])->name(SitePublicRoutesEnum::IpRestriction->value);
    Route::get('/index.html', [IpRestrictionController::class, 'index'])->name(SitePublicRoutesEnum::IpRestriction->value);
    Route::get('/redirect', function () {
        return redirect(SitePublicRoutesEnum::Dashboard->url(), 301);
    })->name(SitePublicRoutesEnum::IpRestrictionRedirect->value);
    // Route::get('/redirect', [IpRestrictionController::class, 'showSiteLink'])->name(SitePublicRoutesEnum::IpRestrictionRedirect->value);
});

Route::middleware(['IsCommunityActive', 'ForceRedirectClient'])->group(function () {


    Route::get('/', [SiteMainPageController::class, 'index'])->name(SitePublicRoutesEnum::MainPage->value);

    Route::get('/login', function () {
        return redirect(SitePublicRoutesEnum::DefaultLogin()->route());
    });

    Route::get('/register', function () {
        return redirect(SitePublicRoutesEnum::RegisterBetconstruct->route());
    });

    Route::get('/forgot-password', function () {
        return redirect(SitePublicRoutesEnum::ForgotPasswordBetconstruct->route());
    });

    // Routes that do not require login
    Route::prefix('auth')->middleware(['GeustUser', 'LastVisitedPageUrl'])->group(function () {

        Route::prefix('bc')->group(function () {

            Route::prefix('register')->group(function () {

                Route::get('/', [RegisterBetconstructController::class, 'index'])->name(SitePublicRoutesEnum::RegisterBetconstruct->value);
                Route::post('/', [RegisterBetconstructController::class, 'attempt'])->middleware('throttle:15,1')->name(SitePublicRoutesEnum::RegisterBetconstruct->value);
                Route::get('/back', [RegisterBetconstructController::class, 'goBackStepRequest'])->name(SitePublicRoutesEnum::RegisterBetconstructGoBack->value);
            });
            Route::prefix('forgot-password')->group(function () {

                Route::get('/', [ForgotPasswordController::class, 'index'])->name(SitePublicRoutesEnum::ForgotPasswordBetconstruct->value);
                Route::get('/recovery', [ForgotPasswordController::class, 'recoveryMethodPage'])->name(SitePublicRoutesEnum::ForgotPasswordRecoveryMethod->value);
                Route::post('/', [ForgotPasswordController::class, 'attempt'])->name(SitePublicRoutesEnum::ForgotPasswordRecoveryAttemp->value);
                Route::get('/verifiy', [ForgotPasswordController::class, 'verificationPage'])->name(SitePublicRoutesEnum::ForgotPasswordVerifiyPage->value);
                Route::post('/verifiy', [ForgotPasswordController::class, 'verificationAttemp'])->name(SitePublicRoutesEnum::ForgotPasswordVerifiyAttemp->value);
                Route::get('/reset_password', [ForgotPasswordController::class, 'resetPasswordPage'])->name(SitePublicRoutesEnum::ForgotPasswordResetPasswordPage->value);
                Route::post('/reset_password', [ForgotPasswordController::class, 'resetPasswordAttemp'])->name(SitePublicRoutesEnum::ForgotPasswordResetPasswordAttemp->value);
            });
            Route::prefix('login_ap')->group(function () {

                Route::get('/', [LoginBetconstructApiController::class, 'index'])->name(SitePublicRoutesEnum::LoginBetconstructApi->value);
                Route::post('/', [LoginBetconstructApiController::class, 'attempt'])->middleware('throttle:5,1')->name(SitePublicRoutesEnum::LoginBetconstructApi->value);
            });
            Route::prefix('login_ws')->group(function () {

                Route::get('/', [LoginBetconstructWebSocketController::class, 'index'])->name(SitePublicRoutesEnum::LoginBetconstructWebSocket->value);
            });

            Route::prefix('sso/betcart')->group(function () {

                Route::get('/{sessionId}', function ($sessionId) {

                    // fake url for test
                    $data = [
                        'userId' => App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\TestPlayerEnum::Id->value,
                        'sessionId' => $sessionId
                    ];
                    $data['hash'] = HashHmacHelper::ComputeHMAC(json_encode($data), 'Fzagnc4yuP4wvCZX');

                    return redirect(route('Auth.SSO.Betcart.Callback', $data));
                })->name('Auth.SSO.Betcart.Redirect');

                Route::get('callback/{userId}/{sessionId}/{hash}', [BetconstructSSOController::class, 'login'])->name('Auth.SSO.Betcart.Callback');
            });
        });
    });

    Route::get('/search/{keyword?}', [SiteSearchController::class, 'index'])->name(SitePublicRoutesEnum::Search->value);

    Route::prefix('/support')->group(function () {
        Route::get('/chatbot', [ChatbotMessengerController::class, 'index'])->name(SitePublicRoutesEnum::Support_Chatbot->value);
    });

    Route::prefix('post')->group(function () {
        Route::get('/article/{articlePost}-{slug}', [PostShowController::class, 'showArticle'])->name(SitePublicRoutesEnum::PostArticle->value);
        Route::get('/faq/{faqPost}-{slug}', [PostShowController::class, 'showFaq'])->name(SitePublicRoutesEnum::PostFaq->value);
    });

    Route::get('/content/{postGroup}-{slug}', [PostGroupContentDisplayController::class, 'show'])->name(SitePublicRoutesEnum::PostGroupContentDispaly->value);

    Route::get('referral/id-{referredBy}', function ($referredBy) {

        if (Referral::where(ReferralsTableEnum::ReferralId->dbName(), $referredBy)->exists())
            GeneralSessionsEnum::SiteRegistrationReferredBy->setSession($referredBy);

        return redirect(SitePublicRoutesEnum::RegisterBetconstruct->url());
    })->name(SitePublicRoutesEnum::Referral_Link->value);

    // Routes that require login
    Route::middleware(['auth'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name(SitePublicRoutesEnum::Dashboard->value);

        Route::get('/logout', function () {

            SessionEnumSwarmApi::deleteSessions(true);

            Auth::logout();
            return redirect()->back();
        })->name(SitePublicRoutesEnum::Logout->name);

        Route::prefix('/notifications')->group(function () {

            Route::get('/', [NotificationController::class, 'index'])->name(SitePublicRoutesEnum::Notifications->value);
            Route::delete('/delete', [NotificationController::class, 'destroy'])->name(SitePublicRoutesEnum::Notifications_Delete->value);
            Route::delete('/delete_all', [NotificationController::class, 'destroyAll'])->name(SitePublicRoutesEnum::Notifications_DeleteALl->value);
        });

        Route::prefix('profile')->group(function () {
            Route::get('/', [UserBetconstructProfileController::class, 'edit'])->name(SitePublicRoutesEnum::Profile->value);
            Route::put('/', [UserBetconstructProfileController::class, 'update'])->name(SitePublicRoutesEnum::Profile->value);
        });

        Route::prefix('tickets')->group(function () {

            // Not Used
            Route::get('/my-tickets', [MyTicketController::class, 'index'])->name(SiteRoutesEnum::Tickets_MyTickets->value);
            // Not Used
            Route::get('/my-ticket-{myTicket}', [ClientTicketMessengerController::class, 'indexClient'])->name(SitePublicRoutesEnum::Tickets_TicketShow->value);
        });

        Route::prefix('referral')->group(function () {

            Route::get('/panel', [ReferralPanelController::class, 'index'])->name(SiteRoutesEnum::Referral_Panel->value);
            Route::put('/claim_reward', [ReferralPanelController::class, 'claimReward'])->name(SitePublicRoutesEnum::Referral_ClaimReward->value);
        });
    });
});
