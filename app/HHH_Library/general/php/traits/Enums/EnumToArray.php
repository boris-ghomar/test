<?php

namespace App\HHH_Library\general\php\traits\Enums;

use App\HHH_Library\general\php\ClassHelper;
use App\HHH_Library\general\php\Enums\LocaleEnum;

trait  EnumToArray
{


    /**
     * Get the case names as array
     * For pure and backed enaums
     *
     * @return array
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get the case values as array
     * For backed enaums
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the case as name=>value in array format
     *
     * @return array
     */
    public static function array(): array
    {
        $names = self::names();
        $values = self::values();

        return (count($names) === count($values)) ?  array_combine(self::names(), self::values()) : self::names();
    }

    /**
     * Get cases as name=>value in json string format
     *
     * @param  bool $PrettyPrint
     * @return string
     */
    public static function json(bool $PrettyPrint = false): string
    {
        return $PrettyPrint ? json_encode(self::array(), JSON_PRETTY_PRINT) : json_encode(self::array());
    }

    /**
     * Get translated array
     *
     * -----------------------------------------------------------
     * Customize for display:
     *
     * To display the list items as translated, you need to create a
     * "translate" function inside the Enum.
     * This function specifies how to display the item in the translated array.
     *
     * use App\Interfaces\Translatable;
     * use "implements Translatable" in enum
     *
     * Example:
     *  public function translate(LocaleEnum $locale = null): ?string
     *  {
     *       return match ($this) {
     *          self::viewAny       => __('general.permissionsData.Abilities.viewAny'),
     *      };
     *  }
     *
     *
     * @param bool $usetranslateAsKey
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum|null $locale
     * @return array
     */
    public static function translatedArray(bool $usetranslateAsKey = true, LocaleEnum $locale = null): array
    {
        if (ClassHelper::hasMethod(get_class(), 'translate')) {

            $isBackedEnum = (count(self::names()) === count(self::values()));

            $translatedArray = [];

            foreach (self::cases() as $case) {

                $translate = $case->translate($locale);

                if (!is_null($translate)) {

                    if ($usetranslateAsKey) {

                        $translatedArray[$translate] = $isBackedEnum ? $case->value : $case->name;
                    } else {
                        if ($isBackedEnum)
                            $translatedArray[$case->value] = $translate;
                        else
                            $translatedArray[$case->name] = $translate;
                    }
                }
            }

            return $translatedArray;
        }

        return self::array();
    }
}
