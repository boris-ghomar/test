<?php

namespace App\Enums\General;

use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;

enum PartnerEnum implements Translatable
{
    use EnumActions;

    /**
     * NOTE
     *
     * In case of update, the database tables that have used
     * this Enum should be searched and updated.
     */

    case Betconstruct;


    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Enum.PartnerEnum.' . $this->name, [], is_null($locale) ? null : $locale->value);
    }
}
