<?php

namespace App\Rules\General\Restriction;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AgeGreaterThan implements ValidationRule
{
    private $minAge;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($minAge = 18)
    {
        $this->minAge = $minAge;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (\Carbon\Carbon::now()->diff(new \Carbon\Carbon($value))->y < $this->minAge)
            $fail('validation.custom.age.gte')->translate(['value' => $this->minAge]);
    }
}
