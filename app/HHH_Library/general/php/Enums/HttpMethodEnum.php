<?php

namespace App\HHH_Library\general\php\Enums;

use App\HHH_Library\general\php\traits\Enums\EnumToArray;

enum HttpMethodEnum: string
{
    use EnumToArray;

    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case DELETE = 'delete';
    case HEAD = 'head';

}
