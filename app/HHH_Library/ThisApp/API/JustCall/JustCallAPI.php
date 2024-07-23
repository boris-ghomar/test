<?php

namespace App\HHH_Library\ThisApp\API\JustCall;

use App\HHH_Library\ThisApp\API\JustCall\Helpers\Requests\SendTextRequest;

class JustCallAPI
{

    /************** Api end-points **************/

    /**
     * Send SMS message
     *
     * @param  string $to
     * @param  string $body
     * @return \App\HHH_Library\ThisApp\API\JustCall\Helpers\Requests\SendTextRequest
     */
    public static function sendText(string $to, string $body): SendTextRequest
    {
        return (new SendTextRequest($to, $body))->send();
    }

    /************** Api end-points END **************/
}
