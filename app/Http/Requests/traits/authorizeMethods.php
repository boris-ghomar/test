<?php

namespace App\Http\Requests\traits;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\HHH_Library\general\php\Enums\HttpMethodEnum;

trait  authorizeMethods
{
    /**
     * Default Authorize Method
     *
     * This function checks the sent request method to match the request.
     * The rest of the investigations are done at the police station.
     *
     * @param  string $modelClass
     * @return bool
     */
    protected function defaultAuthorize(string $modelClass): bool
    {
        $primaryKey = (new $modelClass)->getKeyName();

        $item = $modelClass::find($this->input($primaryKey));

        switch ($this->method()) {
                // insert
            case HttpMethodEnum::POST->name:
                return $this->user()->can(PermissionAbilityEnum::create->name, $modelClass);
                // update
            case HttpMethodEnum::PUT->name:
                return !is_null($item) && $this->user()->can(PermissionAbilityEnum::update->name, $item);
                // destroy
            case HttpMethodEnum::DELETE->name:
                return !is_null($item)
                    && ($this->user()->can(PermissionAbilityEnum::delete->name, $item)
                        || $this->user()->can(PermissionAbilityEnum::forceDelete->name, $item)
                    );
            default:
                return false;
        }

        return false;
    }


    /**
     * Default Authorize Method By Custom Auth Class
     *
     * This function checks the sent request method to match the request.
     * The rest of the investigations are done at the police station.
     *
     * If your authentication class is something other than police, you can use this function.
     *
     * @param  string $modelClass
     * @param  string $authClass
     * @return bool
     */
    protected function defaultAuthorizeByCustomAuthClass(string $modelClass, string $authClass): bool
    {
        $primaryKey = (new $modelClass)->getKeyName();

        $item = $modelClass::find($this->input($primaryKey));

        switch ($this->method()) {
                // insert
            case HttpMethodEnum::POST->name:
                return $this->user()->can(PermissionAbilityEnum::create->name, $authClass);
                // update
            case HttpMethodEnum::PUT->name:
                return !is_null($item) && $this->user()->can(PermissionAbilityEnum::update->name, $authClass);
                // destroy
            case HttpMethodEnum::DELETE->name:
                return !is_null($item)
                    && ($this->user()->can(PermissionAbilityEnum::delete->name, $authClass)
                        || $this->user()->can(PermissionAbilityEnum::forceDelete->name, $authClass)
                    );
            default:
                return false;
        }

        return false;
    }
}
