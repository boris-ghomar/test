<?php

namespace App\Enums\Referral;

use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;

enum ReferralRewardTypeEnum implements Translatable
{
    use EnumActions;

    case CashBack;
    case Bonus;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Enum.ReferralRewardTypeEnum.' . $this->name, [], is_null($locale) ? null : $locale->value);
    }
}
