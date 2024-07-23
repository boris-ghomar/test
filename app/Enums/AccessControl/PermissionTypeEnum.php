<?php

namespace App\Enums\AccessControl;

use App\Interfaces\Translatable;
use App\Enums\Routes\AdminRoutesEnum;
use App\Enums\Routes\SiteRoutesEnum;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum PermissionTypeEnum implements Translatable
{
    use EnumActions;

    case AdminPanel;
    case Site;


    /**
     * Get routes enum calss related to case
     *
     * @return ?string
     */
    public function getRoutesClass(): ?string
    {

        return match ($this) {

            self::AdminPanel    => AdminRoutesEnum::class,
            self::Site          => SiteRoutesEnum::class,

            default => null
        };
    }

    /**
     * Get routes enum cases related to case
     *
     * @return array
     */
    public function getRoutesCases(): array
    {
        $routesClass = $this->getRoutesClass();
        return is_null($routesClass) ? [] : $routesClass::cases();
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
            self::AdminPanel    => __('thisApp.Enum.PermissionTypeEnum.AdminPanel'),
            self::Site          => __('thisApp.Enum.PermissionTypeEnum.Site'),
        };
    }
}
