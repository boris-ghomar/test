<?php

namespace App\Rules\General\StringPattern;

use App\HHH_Library\general\php\Enums\PregPatternValidationEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinOneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pregPattern = PregPatternValidationEnum::MinOneNumber;

        if (!$pregPattern->validate($value))
            $fail($pregPattern->error())->translate();
    }
}
