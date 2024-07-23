<?php

namespace App\Enums\Users;

use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;

enum ClientCategoryMapTypesEnum implements Translatable
{
    use EnumActions;

    case CustomCategory;
    case LoyaltyLevel;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */

    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Enum.ClientCategoryMapTypesEnum.' . $this->name, [], is_null($locale) ? null : $locale->value);
    }
}
