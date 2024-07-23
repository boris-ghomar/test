<?php

namespace App\Http\Middleware\HHH\thisApp;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Models\BackOffice\Settings\Setting;
use App\Models\User;
use App\Policies\BackOffice\AccessControl\WhenCommunityIsInactivePolicy;
use Closure;
use Exception;
use Illuminate\Http\Request;

class IsCommunityActive
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
        $isCommunityActive = Setting::get(AppSettingsEnum::IsCommunityActive);

        if ($isCommunityActive)
            return $next($request);

        if (auth()->check()) {

            /** @var User $user */
            $user = $request->user();

            if ($isCommunityActive || $user->isSuperAdmin() || $user->can(PermissionAbilityEnum::viewAny->name, WhenCommunityIsInactivePolicy::class)) {

                return $next($request);
            }
        }

        $statusCode = HttpResponseStatusCode::ServiceUnavailable->value;
        $message = trans('thisApp.Errors.Settings.AppIsInactive');

        // API
        if ($request->is('api/*')) {

            return JsonResponseHelper::errorResponse(
                'Errors.Settings.AppIsInactive',
                sprintf("%s\n%s", $message, Setting::get(AppSettingsEnum::CommunityExplanationInactive)),
                $statusCode,
            );
        }

        // WEB
        $data = [
            'statusCode' => $statusCode,
            'statusMessage' => HttpResponseStatusCode::getMessageByCode($statusCode),
            'messages' => [$message, Setting::get(AppSettingsEnum::CommunityExplanationInactive)],
        ];
        return response(view('hhh.Site.super_error', $data), $statusCode);
    }
}
