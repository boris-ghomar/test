<?php

namespace App\Http\Middleware\HHH\general;

use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use Closure;
use Illuminate\Http\Request;

class HttpsProtocolMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->secure() && config('app.APP_SSL')) {

            if ($request->is('api/*')) {
                /**
                 * For security reasons,
                 * HTTP requests will not be answered in the API.
                 */
                return JsonResponseHelper::errorResponse(
                    'ApiClient.badRequest.invalidRequestProtocol',
                    trans('ApiClient.badRequest.invalidRequestProtocol'),
                    HttpResponseStatusCode::HttpVersionNotSupported->value,
                );
            }

            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
