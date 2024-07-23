<?php

namespace App\HHH_Library\general\php\traits;


trait  TranslateRouteName
{

    /**
     * translate Route Name
     *
     * Arrows site source: 🢃
     * https://fsymbols.com/signs/arrow/
     *
     * @param array $routeName
     * @return string
     */
    protected function transRouteName(array $routeName): string
    {

        $res = "";

        foreach ($routeName as $name) {

            if ($res != ""){

                $res .= " -> ";
                // trans('general.locale.direction') === "ltr" ? $res .= "🢂" : $res .= "🢀";
            }

            $res .= trans($name);
        }
        return $res;
    }
}
