<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Traits;

trait FilterableEnum
{
    /**
     * Prepare case for merge in filter array
     * with other cases.
     *
     * @param  mixed $value
     * @return array
     */
    public function filter(mixed $value)
    {
        return [$this->name => $this->cast($value)];
    }
}
