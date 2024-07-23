<?php

namespace App\HHH_Library\general\php\traits;


trait  AddAttributesPad
{

    /**
     * This function inserts the string into the desired pad.
     *
     * @param  string $str
     * @param  string $pad
     * @return string
     */
    public static function addPadToString($str, $pad = "'")
    {
        $str = ($str == null) ? "" : $str;
        return sprintf("%s%s%s", $pad, $str, $pad);
    }

    /**
     * This function inserts the corresponding
     * values of the array keys into the desired pad.
     *
     * @param array $array
     * @param  string $pad
     * @return array
     */
    public static function addPadToArrayVal(array $array, $pad = "'")
    {
        foreach ($array as $key => $value) {
            $array[$key] = self::addPadToString($value, $pad);
        }

        return $array;
    }
}
