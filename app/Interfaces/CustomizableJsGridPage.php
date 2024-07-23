<?php

namespace App\Interfaces;


interface CustomizableJsGridPage
{

    /**
     * Define page route
     *
     * @return object : \App\Enums\Routes\AdminRoutesEnum|\App\Enums\Routes\AdminPublicRoutesEnum|\App\Enums\Routes\SiteRoutesEnum|\App\Enums\Routes\SitePublicRoutesEnum
     */
    public static function customizablePageRoute(): object;

    /**
     * Get customizable page required columns
     *
     * @return array
     */
    public static function getCustomizablePageRequiredColumns(): array;

    /**
     * Get customizable page selectable columns
     *
     * @return array
     */
    public static function getCustomizablePageSelectableColumns(): array;
}
