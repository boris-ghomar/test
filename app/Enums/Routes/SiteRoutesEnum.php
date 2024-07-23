<?php

namespace App\Enums\Routes;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\HHH_Library\general\php\traits\Enums\EnumRoutsAction;
use App\HHH_Library\general\php\traits\TranslateRouteName;

enum SiteRoutesEnum: string implements Translatable
{
    use EnumActions;
    use TranslateRouteName;
    use EnumRoutsAction;

    case Tickets_MyTickets = "Site.Tickets.MyTickets";
    case Referral_Panel = "Site.Referral.Panel";

    /**
     * abilities
     *
     * @return array
     */
    public function abilities(): array
    {
        $viewAny        = PermissionAbilityEnum::viewAny->name;
        $view           = PermissionAbilityEnum::view->name;
        $create         = PermissionAbilityEnum::create->name;
        $update         = PermissionAbilityEnum::update->name;
        $delete         = PermissionAbilityEnum::delete->name;
        $forceDelete    = PermissionAbilityEnum::forceDelete->name;

        return match ($this) {

            self::Tickets_MyTickets => [$viewAny],
            self::Referral_Panel => [$viewAny],
        };
    }

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {

        return match ($this) {

            self::Tickets_MyTickets => $this->transRouteName(['bo_navbar.Tickets.MenuTitle', 'bo_navbar.Tickets.MyTickets']),
            self::Referral_Panel => $this->transRouteName(['bo_navbar.Referral.MenuTitle', 'bo_navbar.Referral.ReferralPanel']),

            default => $this->name
        };
    }
}
