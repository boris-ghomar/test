<?php

namespace App\Rules\General\Restriction;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class AllowedCountry implements ValidationRule
{

    protected $ignoreCases;

    /**
     * Create a new rule instance.
     *
     * @param array $ignoreCases An array of things to ignore,like as 'global'
     * @return void
     */
    public function __construct(array $ignoreCases = [])
    {
        $this->ignoreCases = $ignoreCases;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Arr::has(trans('countries', [], 'en'), $value) && !in_array($value, $this->ignoreCases))
            $fail('validation.custom.notDefined')->translate();
    }
}
