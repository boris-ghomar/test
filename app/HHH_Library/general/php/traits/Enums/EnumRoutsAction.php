<?php

namespace App\HHH_Library\general\php\traits\Enums;

use Illuminate\Contracts\Routing\UrlGenerator;

trait  EnumRoutsAction
{


    /**
     * Get route of case
     *
     * @param  mixed $parameters
     * @param  bool $absolute
     * @return ?string
     */
    public function route(mixed $parameters = [], bool $absolute = true): ?string
    {
        return route($this->value, $parameters, $absolute);
    }

    /**
     * Get URL of case
     *
     * @param  mixed $parameters
     * @param  bool|null $secure
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function url(mixed $parameters = [], bool|null $secure = null): UrlGenerator|string
    {
        return url($this->route($parameters), [], $secure);
    }
}
