<?php

namespace App\Http\Middleware\HHH\thisApp;

use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Users\ClientProfileCheckEnum;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceRedirectClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // API request
        if ($request->is('api/*'))
            return $next($request);

        /** @var User $user */
        $user = $request->user();

        if (is_null($user)) // Guest user
            return $next($request);

        if (!$user->isClient())
            return $next($request);

        $routeName = $request->route()->getName();
        if ($routeName == SitePublicRoutesEnum::Logout->name)
            return $next($request);

        $requestUrl = $request->url();

        if (!$this->checkClientProfile($user)) {
            $profileUrl = SitePublicRoutesEnum::Profile->url();

            return ($requestUrl == $profileUrl) ? $next($request) : redirect(SitePublicRoutesEnum::Profile->url());
        }

        return $next($request);
    }

    /**
     * Check client profile
     *
     * @param  \App\Models\User $user
     * @return bool
     */
    private function checkClientProfile(User $user): bool
    {
        if (ClientProfileCheckEnum::ProfileRequiredItems->isCompleted($user))
            return true;
        else
            return false;
    }
}
