<?php

namespace App\Http\Middleware;

use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Enums\Routes\RouteTypesEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        /****** HHHE ******/
        if ($request->expectsJson())
            return null;

        return match (RouteTypesEnum::type()) {

            RouteTypesEnum::AdminPanel  => AdminPublicRoutesEnum::Login->route(),
            RouteTypesEnum::Site        => SitePublicRoutesEnum::DefaultLogin()->route(),

            default => SitePublicRoutesEnum::DefaultLogin()->route()
        };

        /****** HHHE END ******/

        // return $request->expectsJson() ? null : route('login');
    }
}
