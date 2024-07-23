<?php

namespace App\Enums\Referral;

use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Interfaces\Translatable;

enum ReferralSessionStatusEnum implements Translatable
{
    use EnumActions;

    case PayingRewards;
    case InProgress;
    case Upcoming;
    case Finished;

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Enum.ReferralSessionStatusEnum.' . $this->name, [], is_null($locale) ? null : $locale->value);
    }

    /**
     * Get status items that allowed to delete
     *
     * @param  bool $returnName
     * @return array
     */
    public static function getAllowedToDelete(bool $returnName = true): array
    {
        $items = [
            self::Upcoming,
            self::Finished,
        ];

        if ($returnName) {

            $list = [];

            foreach ($items as $item) {
                array_push($list, $item->name);
            }
        } else
            $list =  $items;

        return $list;
    }
}
