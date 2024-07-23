<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers;

use App\HHH_Library\general\php\CurlHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ApiConfigEnum;

class ApiRequestHandeler extends CurlHelper
{

    public function __construct(string $url)
    {
        $this->setUrl($url);
        $this->setHeaders(ApiConfigEnum::headers());
        $this->setRequestMethod(ApiConfigEnum::RequestMethod->getValue());

        $this->init();
    }
}
