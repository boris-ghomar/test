<?php

namespace App\Http\Middleware\HHH\api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class apiLocalization
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
        $locale = config('app.locale'); // default locale

        if ($request->hasHeader('locale')) {

            $reqLocale = $request->Header('locale');

            if (in_array($reqLocale, config('app.available_locales'))) {
                $locale = $reqLocale;
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
