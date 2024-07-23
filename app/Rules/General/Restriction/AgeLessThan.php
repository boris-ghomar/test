<?php

namespace App\Rules\General\Restriction;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AgeLessThan implements ValidationRule
{
    private $maxAge;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($maxAge = 100)
    {
        $this->maxAge = $maxAge;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (\Carbon\Carbon::now()->diff(new \Carbon\Carbon($value))->y > $this->maxAge)
            $fail('validation.custom.age.lte')->translate(['value' => $this->maxAge]);
    }
}
