<?php

namespace App\Enums\UserActions;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum CommentableTypesEnum
{
    use EnumActions;

    case Post;
    case Comment;
}
