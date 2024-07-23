<?php

namespace App\Enums\AccessControl;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum PostActionsEnum implements Translatable
{
    use EnumActions;

    case View;
    case Like;
    case Comment;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return match ($this) {

            self::View      => __('thisApp.PostActions.View'),
            self::Like      => __('thisApp.PostActions.Like'),
            self::Comment   => __('thisApp.PostActions.Comment'),
        };
    }
}
