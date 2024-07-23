<?php

namespace App\Rules\General\StringPattern;

use App\HHH_Library\general\php\Enums\PregPatternValidationEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinOneSpecialCharacter implements ValidationRule
{

    private $specialCharacters;

    /**
     * Create a new rule instance.
     *
     * @param  string $notAllowedCharacter
     * @return void
     */
    public function __construct(string $specialCharacters = '#?!@$%^&*-')
    {
        $this->specialCharacters = $specialCharacters;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $pregPattern = PregPatternValidationEnum::MinOneSpecialCharacter;

        $pattern = sprintf("(^(?=.*?[%s]).+$)", $this->specialCharacters);

        if (!$pregPattern->validate($value, $pattern))
            $fail($pregPattern->error())
                ->translate(['specialCharacter' => $this->specialCharacters]);

    }
}
