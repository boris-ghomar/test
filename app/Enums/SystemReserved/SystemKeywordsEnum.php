<?php

namespace App\Enums\SystemReserved;

use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;

enum SystemKeywordsEnum implements Translatable
{
    use EnumActions;

    case SystemSettings;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Enum.SystemReservedEnum.' . $this->name, [], is_null($locale) ? null : $locale->value);
    }
}
