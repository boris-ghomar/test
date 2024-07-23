<?php

namespace App\Enums\Routes;

use App\HHH_Library\general\php\traits\Enums\EnumActions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

enum RouteTypesEnum
{
    use EnumActions;

    case AdminPanel;
    case Site;

    /**
     * Get session key based on current route
     *
     * @return \App\Enums\Session\LocaleKeyEnum
     */
    public static function type(): self
    {
        $route = Route::getCurrentRoute();

        if (is_null($route)) return self::Site;

        $routeUri = $route->uri();

        $adminRoutesStart = [
            config('app.admin_routes_prefix') . '/', // web routes
            config('hhh_config.apiBaseUrls.backoffice.javascript'), //javascript api routes
        ];

        return Str::of($routeUri)->startsWith($adminRoutesStart) ? self::AdminPanel : self::Site;
    }

    /**
     * Get current route name
     *
     * @return ?string
     */
    public static function getCurrentRouteName(): ?string
    {
        $currentRoute = Route::getCurrentRoute();
        return is_null($currentRoute) ? null : $currentRoute->getName();
    }

    /**
     * Is the current route the admin panel route?
     *
     * @return bool
     */
    public static function isAdminRoute(): bool
    {
        return self::type() === self::AdminPanel;
    }

    /**
     * Is the current route the site(community) route?
     *
     * @return bool
     */
    public static function isSiteRoute(): bool
    {
        return self::type() === self::Site;
    }
}
