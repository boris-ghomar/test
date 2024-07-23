<?php

namespace App\Enums\Database\Tables;

use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\general\php\traits\Enums\EnumToDatabaseColumnName;

enum ApiNewAttrinutesTableEnum
{
    use EnumActions;
    use EnumToDatabaseColumnName;

    case Id;
    case ClassName;
    case Attrinute;
    case Values;
    case Descr;
}
