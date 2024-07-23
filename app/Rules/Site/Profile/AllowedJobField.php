<?php

namespace App\Rules\Site\Profile;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class AllowedJobField implements ValidationRule
{

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Arr::has(trans('thisApp.JobFields', [], 'en'), $value))
            $fail('validation.custom.notExist')->translate();
    }
}
