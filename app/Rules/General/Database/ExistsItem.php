<?php

namespace App\Rules\General\Database;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistsItem implements ValidationRule
{
    private $modelClass;
    private $message;

    /**
     * Create a new rule instance.
     *
     * @param  string $modelClass
     * @param  ?string $message
     * @return void
     */
    public function __construct(string $modelClass, ?string $message = null)
    {
        $this->modelClass = $modelClass;
        $this->message = $message;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if (!$this->modelClass::find($value)) {

            if (empty($this->message))
                $fail('general.NotFoundItem')->translate();
            else
                $fail($this->message);
        }
    }
}
