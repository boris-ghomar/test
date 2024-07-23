<?php

namespace App\Http\Middleware\HHH\api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;


class checkPaginationData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $warningsKey = Config::get('hhh_config.keywords.warnings');
        $pageSizeKey = Config::get('hhh_config.keywords.pageSize');
        $pageIndexKey = Config::get('hhh_config.keywords.pageIndex');

        $pageSize = (int) $request->input($pageSizeKey);
        $pageIndex = (int) $request->input($pageIndexKey);


        if ($pageSize > 100) {
            $pageSize = 100;

            $request->merge([
                $warningsKey  =>  "The number of requested records per page is more than the maximum allowed. Therefore, it has been changed to the maximum allowed: " . $pageSize . " records."
            ]);
        } else if ($pageSize < 1) {
            $pageSize = 1;

            $request->merge([
                $warningsKey  =>  "The number of requested records per page is less than the minimum allowed. Therefore, it has been changed to the minimum allowed: " . $pageSize . " record."
            ]);
        }

        if ($pageIndex < 1) {
            $pageIndex = 1;
        }

        $request->merge([
            $pageIndexKey =>  $pageIndex,
            $pageSizeKey =>  $pageSize,
        ]);

        return $next($request);
    }
}
