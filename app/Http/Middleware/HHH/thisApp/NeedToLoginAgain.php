<?php

namespace App\Http\Middleware\HHH\thisApp;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NeedToLoginAgain
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
        $expirationDays = 3;

        if (auth()->check()) {

            /** @var User $user */
            $user = auth()->user();

            if ($user->isClient()) {

                if (is_null($user->userExtra)) {
                    Auth::logout();
                } else {

                    // Check client last updated infromation date
                    $userExtra = $user->userExtra()
                        ->where(TimestampsEnum::UpdatedAt->dbName(), "<", Carbon::now()->subDays($expirationDays))
                        ->first();

                    if (!is_null($userExtra)) {
                        Auth::logout();
                    }
                }
            }
        }

        return $next($request);
    }
}
