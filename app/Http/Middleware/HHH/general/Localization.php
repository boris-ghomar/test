<?php

namespace App\Http\Middleware\HHH\general;

use App\Enums\Session\LocaleKeyEnum;
use Closure;
use Illuminate\Http\Request;

class Localization
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
        LocaleKeyEnum::setupSessionLocale();

        return $next($request);
    }
}
