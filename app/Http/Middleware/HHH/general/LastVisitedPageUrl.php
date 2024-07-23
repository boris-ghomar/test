<?php

namespace App\Http\Middleware\HHH\general;

use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Session\GeneralSessionsEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

class LastVisitedPageUrl
{

    /**
     * File Owner:: HHH
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $previousUrl = (url()->previous(SitePublicRoutesEnum::MainPage->url()));

        if (!Str::of($previousUrl)->contains(config('app.domain'))) {
            // Redirected from another site
            $previousUrl = Redirect::intended()->getTargetUrl();
        }

        try {
            $routes = Route::getRoutes();
            $testRequest = Request::create($previousUrl);

            $route = $routes->match($testRequest);

            if ($route->uri() == "{fallbackPlaceholder}")
                $previousUrl = SitePublicRoutesEnum::MainPage->url();

            // route exists
        } catch (\Throwable $th) {
            // route doesn't exist
            $previousUrl = SitePublicRoutesEnum::MainPage->url();
        }

        GeneralSessionsEnum::LastVisitedPageUrl->setSession($previousUrl);

        return $next($request);
    }
}
