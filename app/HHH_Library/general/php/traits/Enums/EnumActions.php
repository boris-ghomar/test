<?php

namespace App\HHH_Library\general\php\traits\Enums;

use Illuminate\Support\Str;

trait  EnumActions
{
    use EnumToArray;

    private const KEY_NAME = "name";
    private const KEY_VALUE = "value";

    /**
     * Check enum has "name" or "value"
     *
     * @param  string|int|null $needle
     * @param  bool $caseSensitive
     * @param  string $key
     * @return bool
     */
    private static function has(string|int|null $needle, bool $caseSensitive = true, string $key = self::KEY_NAME): bool
    {
        if (is_null($needle))
            return false;

        $data = ($key === self::KEY_NAME) ? self::names() : self::values();

        if (!is_numeric($needle)) {

            if (!$caseSensitive) {

                $data = array_map('strtolower', $data);
                $needle = strtolower($needle);
            }
        }

        return in_array($needle, $data);
    }

    /**
     * Check enum has "name"
     *
     * @param  ?string $needle
     * @param  bool $caseSensitive
     * @return bool
     */
    public static function hasName(?string $needle, bool $caseSensitive = true): bool
    {
        return self::has($needle, $caseSensitive, self::KEY_NAME);
    }

    /**
     * Check enum has "value"
     *
     * @param  string|int|null $needle
     * @param  bool $caseSensitive
     * @return bool
     */
    public static function hasValue(string|int|null $needle, bool $caseSensitive = true): bool
    {
        return self::has($needle, $caseSensitive, self::KEY_VALUE);
    }


    /**
     * Get case by case name
     *
     * @param  ?string $name
     * @return mixed class
     */
    public static function getCase(?string $name): mixed
    {
        return self::hasName($name) ? constant(__CLASS__ . '::' . $name) : null;
    }

    /**
     * Get case by db column name name
     *
     * @param  ?string $name
     * @return mixed class
     */
    public static function getCaseByDbName(?string $name): mixed
    {
        if (empty($name))
            return null;

        // Get case by studly format
        $case = self::getCase(Str::studly($name));

        if (is_null($case)) {
            // Get case by uppercase format
            $case = self::getCase(Str::upper($name));
        }

        return $case;
    }

    /**
     * Get case by case value
     *
     * @param  int|string|null $value
     * @param bool $caseSensitive
     * @return mixed case
     */
    public static function getCaseByValue(int|string|null $value, bool $caseSensitive = true): mixed
    {
        $needle = $caseSensitive ? $value : strtolower($value);

        foreach (self::cases() as $case) {

            $caseValue = $caseSensitive ? $case->value : strtolower($case->value);

            if ($caseValue === $needle)
                return $case;
        }

        return null;
    }

    /**
     * Get case name by value
     *
     * @param  int|string|null $value
     * @param bool $caseSensitive
     * @return ?string
     */
    public static function getNameByValue(int|string|null $value, bool $caseSensitive = true): ?string
    {
        $case = self::getCaseByValue($value, $caseSensitive);

        return is_null($case) ? null : $case->name;
    }
}
