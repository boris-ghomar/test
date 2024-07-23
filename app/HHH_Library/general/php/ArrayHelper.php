<?php

namespace App\HHH_Library\general\php;


/**
 * General and usable functions
 *
 *
 */

class ArrayHelper
{

    /**
     * Change keys case of array
     *
     * @param  ?array $array
     * @param  bool $toLowerCase true ? lowerCase : upperCase
     * @return array
     */
    public static function changeKeyCase(?array $array, bool $toLowerCase = true): array
    {
        if (is_null($array)) return [];
        if (empty($array)) return [];

        if (array_is_list($array))
            return self::changeValueCase($array, $toLowerCase);

        return array_change_key_case($array, $toLowerCase ? CASE_LOWER : CASE_UPPER);
    }

    /**
     * Change values case of array
     *
     * @param  ?array $array
     * @param  bool $toLowerCase true ? lowerCase : upperCase
     * @return array
     */
    public static function changeValueCase(?array $array, bool $toLowerCase = true): array
    {
        if (is_null($array)) return [];
        if (empty($array)) return [];

        return array_map($toLowerCase ? 'strtolower' : 'strtoupper', $array);
    }

    /**
     * This function removes items in $removeList from the $array
     *
     * @param ?array $array
     * @param ?array $removeList
     * @return array
     */
    public static function removeItems(?array $array, ?array $removeList): array
    {
        if (empty($array))
            return [];

        if (empty($removeList))
            return $array;

        $res = [];

        if (array_is_list($array)) {

            foreach ($array as $item) {

                if (!in_array($item, $removeList))
                    array_push($res, $item);
            }
        } else {

            foreach ($array as $key => $value) {

                if (!in_array($key, $removeList))
                    $res[$key] = $value;
            }
        }

        return $res;
    }

    /**
     * This function removes empty items
     * from the given array.
     *
     * @param ?array $array
     * @return array
     */
    public static function removeEmptyItems(?array $array): array
    {
        if (is_null($array))
            return [];

        return array_filter($array, fn ($value) => !is_null($value) && $value !== '');
    }

    /**
     * Search array for needle
     *
     * Sample of input array:
     * ['item1', 'item2',...]
     * [['key1' => $value1], ['key2' => $value2],...]
     *
     * @param  mixed $needle
     * @param  array $array
     * @param  bool $strict (optinal)
     * @return string|int|false string(key): if array is $key => $value | int: if array is list | false: if needle not found
     */
    public static function search(mixed $needle, array $array, bool $strict = false): string|int|false
    {
        if (is_null($needle) || !is_array($array) || count($array) == 0)
            return false;

        return array_search($needle, $array, $strict);
    }

    /**
     * Search array for needle [Not case sensitive]
     *
     * Sample of input array:
     * ['item1', 'item2',...]
     * [['key1' => $value1], ['key2' => $value2],...]
     *
     * @param  mixed $needle
     * @param  array $array
     * @param  bool $strict (optinal)
     * @return string|int|false string(key): if array is $key => $value | int: if array is list | false: if needle not found
     */
    public static function searchInsensitiveCase(mixed $needle, array $array, bool $strict = false): string|int|false
    {
        $needle = strtolower($needle);
        $array = self::changeValueCase($array, true);

        return self::search($needle, $array, $strict);
    }

    /**
     * Search array for needle for like as %{$needle}%
     *
     * Sample of input array:
     * ['item1', 'item2',...]
     * [['key1' => $value1], ['key2' => $value2],...]
     *
     * @param  mixed $needle
     * @param  array $array
     * @param  bool $caseSensetive
     * @param  bool $reverse If set to true, this function returns the elements of the input array that do not match the given needle.
     * @return array Items that match with %needle%
     */
    public static function searchLikeAs(mixed $needle, array $array, bool $caseSensetive = true, bool $reverse = false): array
    {
        if (is_null($needle) || !is_array($array) || count($array) == 0)
            return [];

        $pattern = '~' . $needle . '~';

        if (!$caseSensetive) {
            $needle = strtolower($needle);
            $pattern = '~' . $needle . '~i';
        }

        $flags = 0;
        if ($reverse)
            $flags = PREG_GREP_INVERT;

        return preg_grep($pattern, $array, $flags);
    }

    /**
     * Search multi dimentional array.
     *
     * Sample of input array:
     *
     * $options =
     *  [
     *      [
     *          "name" => "",
     *          "key" => ""
     *      ],
     *      [
     *          "name" => "Active User",
     *          "key" => "Active"
     *      ],
     *       [
     *          "name" => "Suspended User"
     *          "key" => "Suspended"
     *      ]
     *  ]
     *
     * Sample Request:
     * ArrayHelper::searchMultiDimentional('Active, 'key', $options);
     *
     * @param  mixed $needle
     * @param  ?string $keyLabel
     * @param  array $array
     * @param  bool $strict (optinal)
     * @return string|int|false string(key): if array is $key => $value | int: if array is list | false: if needle not found
     * @return string|int|false
     */
    public static function searchMultiDimentional(mixed $needle, ?string $keyLabel = 'key', array $array, bool $strict = false): string|int|false
    {
        if (is_null($needle) || empty($keyLabel) || !is_array($array) || count($array) == 0)
            return false;

        $arrayColumn = array_column($array, $keyLabel);

        return array_search($needle, $arrayColumn, $strict);
    }
}
