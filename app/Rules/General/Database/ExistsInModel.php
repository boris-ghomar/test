<?php

namespace App\Rules\General\Database;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistsInModel implements ValidationRule
{
    private $modelClass;
    private $column;

    /**
     * Create a new rule instance.
     *
     * @param  mixed $modelClass
     * @return void
     */
    public function __construct(string $modelClass, string $column)
    {
        $this->modelClass = $modelClass;
        $this->column = $column;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $item = $this->modelClass::where($this->column, $value);

        if (!$item->exists())
            $fail('validation.custom.notExist')->translate();
    }
}
