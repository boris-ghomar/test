<?php

namespace App\HHH_Library\general\php\traits;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Routes\AdminRoutesEnum;
use App\Models\User;
use App\Policies\BackOffice\Global\GlobalViewClientEmailPolicy;
use App\Policies\BackOffice\Global\GlobalViewClientPhonePolicy;

trait  MaskModelAttribute
{

    /**
     * Mask global attribute
     *
     * @param  mixed $value
     * @param  \App\Enums\Routes\AdminRoutesEnum $route
     * @return mixed
     */
    protected function maskGlobalAttribute(mixed $value, AdminRoutesEnum $route): mixed
    {
        $maskedValue = "*****";

        $className = match ($route) {
            AdminRoutesEnum::Global_ViewClientEmail => GlobalViewClientEmailPolicy::class,
            AdminRoutesEnum::Global_ViewClientPhone => GlobalViewClientPhonePolicy::class,

            default => null
        };

        if (is_null($route))
            return $maskedValue;

        return User::authUser()->can(PermissionAbilityEnum::view->name, $className) ? $value : $maskedValue;
    }
}
