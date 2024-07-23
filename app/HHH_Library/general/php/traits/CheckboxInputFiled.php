<?php

namespace App\HHH_Library\general\php\traits;


trait  CheckboxInputFiled
{

    /**
     * This function takes an array as a filter ($filter)
     * and returns the desired checkbox value from within it.
     * If the checkbox with this name($checkboxName) does not exist in the array,
     * it returns the null value as undefinded.
     *
     * true: checked
     * false: unchecked
     * null: undefinded
     *
     * @param array $filter
     * @param  string $checkboxName checkbox key in $filter array
     * @return true|false|null returns null if checkbox not exists in $filter array
     *
     */
    protected function getInputCheckboxValue($filter, $checkboxName)
    {
        if (!array_key_exists($checkboxName, $filter))
            return null;

        $checkboxValue = strtolower($filter[$checkboxName]);
        if (
            $checkboxValue == "true" || $checkboxValue == "false" ||
            $checkboxValue == "1" || $checkboxValue == "0"
        ) {

            // convert string boolean to boolean: "true" => true | "false" => false
            return filter_var($checkboxValue, FILTER_VALIDATE_BOOLEAN);
        }

        return null; // checkbox not exists in $filter array
    }
}
