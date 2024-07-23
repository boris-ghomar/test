<?php

namespace App\Interfaces;

interface Castable
{

    /**
     * Convert the variable cast to the actual cast type.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast(mixed $value): mixed;
}
