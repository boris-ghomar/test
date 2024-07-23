<?php

namespace App\Rules\General\Restriction;

use App\HHH_Library\general\php\Enums\GendersEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedGender implements ValidationRule
{

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!GendersEnum::hasName($value, false))
            $fail('validation.custom.notDefined')->translate();
    }
}
