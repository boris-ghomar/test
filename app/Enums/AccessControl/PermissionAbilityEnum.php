<?php

namespace App\Enums\AccessControl;

use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum PermissionAbilityEnum implements Translatable
{
    use EnumActions;

    /**
     * NOTICE:
     * Don't change the cases to upper or lower,
     * This abilities used by laravel as well
     *
     * based on : https://laravel.com/docs/10.x/authorization#authorizing-resource-controllers
     * Abilities on Controller Methods
     */

    case viewAny;       // index (View index page)
    case view;         // show ( View Details of item )
    case create;        // create & store
    case update;        // edit & update
    case delete;        // soft delete (If it is active, otherwise it will be completely deleted)
    case restore;       // restore deleted item (soft delete)
    case forceDelete;   // force deleted (Used after soft delete)
    case export;        // export (Export data to files such as excel)

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return match ($this) {

            self::viewAny       => __('general.permissionsData.Abilities.viewAny'),
            self::view          => __('general.permissionsData.Abilities.view'),
            self::create        => __('general.permissionsData.Abilities.create'),
            self::update        => __('general.permissionsData.Abilities.update'),
            self::delete        => __('general.permissionsData.Abilities.delete'),
            self::restore       => __('general.permissionsData.Abilities.restore'),
            self::forceDelete   => __('general.permissionsData.Abilities.forceDelete'),
            self::export        => __('general.permissionsData.Abilities.export'),
        };
    }
}
