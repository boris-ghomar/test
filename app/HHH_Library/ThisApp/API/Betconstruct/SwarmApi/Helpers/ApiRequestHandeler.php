<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers;

use App\HHH_Library\general\php\CurlHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ApiConfigEnum;

class ApiRequestHandeler extends CurlHelper
{

    /**
     * __construct
     *
     * @param  string $url
     * @param  array $headers
     * @return void
     */
    public function __construct(string $url, array $headers = [])
    {
        $this->setUrl($url);
        $this->setHeaders($headers);
        $this->setRequestMethod(ApiConfigEnum::RequestMethod->getValue());

        $this->init();
    }
}
