<?php

namespace App\HHH_Library\general\php;


class ClassHelper
{

    /**
     * This function checks whether a class has the desired method.
     *
     * @param  ?string $class Example: test::class
     * @param  ?string $methodName
     *
     * @return boolean
     */
    public static function hasMethod(?string $class, ?string $methodName): bool
    {
        if (empty($class) || empty($methodName))
            return false;

        return method_exists($class, $methodName);
    }

    /**
     * This function checks whether a class has the desired method.
     *
     * @param  ?string $class Example: test::class
     * @param  ?string $traitClass Example: MyTrait::class
     *
     * @return boolean
     */
    public static function hasTrait(?string $class, ?string $traitClass): bool
    {
        if (empty($class) || empty($traitClass))
            return false;

        return in_array(
            $traitClass,
            self::getRecursive($class)
        );
    }

    /**
     * Get recursive of class.
     *
     * @param  ?string $class Example: test::class
     * @return array
     */
     public static function getRecursive(?string $class): array
    {
        if (empty($class))
            return [];

        return class_uses_recursive($class);
    }
}
