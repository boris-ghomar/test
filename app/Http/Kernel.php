<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            //HHHE
            \App\Http\Middleware\HHH\general\HttpsProtocolMiddleware::class,
            \App\Http\Middleware\HHH\general\Localization::class,
            \App\Http\Middleware\HHH\general\auth\CheckBlockedUserAccount::class,
            \App\Http\Middleware\HHH\general\auth\CheckUserExtras::class,
            \App\Http\Middleware\HHH\thisApp\NeedToLoginAgain::class,
            //HHHE END
        ],

        'api' => [
            //HHHE
            // This middleware must be on first so that in exception of occurrence, the exception error is also translated.
            \App\Http\Middleware\HHH\api\apiLocalization::class,
            \App\Http\Middleware\HHH\general\HttpsProtocolMiddleware::class,
            //HHHE END

            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        // HHHE
        'GeustUser' => \App\Http\Middleware\HHH\general\auth\GeustUser::class,
        'BackOfficeUser' => \App\Http\Middleware\HHH\general\auth\BackOfficeUser::class,
        'IsAdminPanelActive' => \App\Http\Middleware\HHH\thisApp\IsAdminPanelActive::class,
        'IsCommunityActive' => \App\Http\Middleware\HHH\thisApp\IsCommunityActive::class,
        'ForceRedirectClient' => \App\Http\Middleware\HHH\thisApp\ForceRedirectClient::class,
        'LastVisitedPageUrl' => \App\Http\Middleware\HHH\general\LastVisitedPageUrl::class,
        // HHHE END
    ];
}
