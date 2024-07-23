<?php

namespace App\HHH_Library\ThisApp\API\JustCall\Helpers;

use App\HHH_Library\general\php\CurlHelper;
use App\HHH_Library\ThisApp\API\JustCall\Enums\ApiConfigEnum;

class ApiRequestHandler extends CurlHelper
{

    public function __construct(string $url)
    {
        $this->setUrl($url);
        $this->setHeaders(ApiConfigEnum::headers());
        $this->setRequestMethod(ApiConfigEnum::RequestMethod->getValue());

        $this->init();
    }
}
