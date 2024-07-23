<?php

namespace App\Rules\General\Restriction;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class AllowedProvince implements ValidationRule
{

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Arr::has(trans('IranCities.Provinces', [], 'en'), $value))
            $fail('validation.custom.notExist')->translate();
    }
}
