<?php

namespace App\HHH_Library\general\php\traits\Enums;

trait  EnumCastParams
{
    /**
     * NOTICE:
     * When using this trait, the "Castable" interface should also be used.
     *
     * \App\Interfaces\Castable
     */

    use EnumActions;

    /**
     * Get the values of array in the right type
     * This function finds the key of item in the name of enum cases
     *
     * Both pure and backed enums
     *
     * @param  array $items format: $key => $value
     * @return array
     */
    public static function castParams(array $items): array
    {
        $res = [];

        foreach ($items as $key => $value) {

            /** @var self $case */
            $case = self::getCase($key);

            if(is_null($case))
                $case = self::getCaseByDbName($key);

            $res[$key] = is_null($case) ? $value : $case->cast($value);
        }

        return $res;
    }

    /**
     * Get the values of array in the right type
     * This function finds the key of item in the value of enum cases
     *
     * Only backed enums
     *
     * @param  array $items format: $key => $value
     * @return array
     */
    public static function castParamsByValue(array $items): array
    {
        $res = [];

        foreach ($items as $key => $value) {

            $case = self::getCaseByValue($key);
            $res[$key] = is_null($case) ? $value : $case->cast($value);
        }

        return $res;
    }
}
