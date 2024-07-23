<?php

namespace App\Enums\Referral;

use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;

enum ReferralRewardPaymentSatusEnum implements Translatable
{
    use EnumActions;

    case InProgress;
    case PaymentQueue;
    case Paid;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Enum.ReferralRewardPaymentSatusEnum.' . $this->name, [], is_null($locale) ? null : $locale->value);
    }
}
