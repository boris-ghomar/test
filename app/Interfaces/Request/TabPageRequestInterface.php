<?php

namespace App\Interfaces\Request;


interface TabPageRequestInterface
{

    /**
     * Get availabel tabs list
     *
     * @return array
     */
    public function tabsList(): array;


}
