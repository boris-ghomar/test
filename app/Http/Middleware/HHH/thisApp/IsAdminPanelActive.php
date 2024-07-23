<?php

namespace App\Http\Middleware\HHH\thisApp;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Models\BackOffice\Settings\Setting;
use App\Models\User;
use App\Policies\BackOffice\AccessControl\WhenAdminPanelIsInactivePolicy;
use Closure;
use Exception;
use Illuminate\Http\Request;

class IsAdminPanelActive
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

        if (auth()->check()) {

            /** @var User $user */
            $user = $request->user();

            if (Setting::get(AppSettingsEnum::IsAdminPanelActive) || $user->isSuperAdmin() || $user->can(PermissionAbilityEnum::viewAny->name, WhenAdminPanelIsInactivePolicy::class)) {

                return $next($request);
            }
        }

        $statusCode = HttpResponseStatusCode::ServiceUnavailable->value;
        $message = trans('thisApp.Errors.Settings.AppIsInactive');

        // API
        if ($request->is('api/*')) {

            return JsonResponseHelper::errorResponse(
                'Errors.Settings.AppIsInactive',
                sprintf("%s\n%s", $message, Setting::get(AppSettingsEnum::AdminPanelExplanationInactive)),
                $statusCode,
            );
        }

        // WEB
        $data = [
            'statusCode' => $statusCode,
            'statusMessage' => HttpResponseStatusCode::getMessageByCode($statusCode),
            'messages' => [$message, Setting::get(AppSettingsEnum::AdminPanelExplanationInactive)],
        ];
        return response(view('hhh.BackOffice.super_error', $data), $statusCode);
    }
}
