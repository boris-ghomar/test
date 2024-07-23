<?php

namespace App\Rules\General\StringPattern;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class NotAllowedCharacter implements ValidationRule
{

    private array $notAllowedCharacters =  array(" ", "!", "[", "]", "^", "'", "£", "$", "%", "^", "&", "*", "(", ")", "}", "{", "@", ":", "#", "~", "?", "<", ">", ",", ";", "@", "|", "\\", "-", "=", "_", "+", "¬", '"');

    /**
     * Create a new rule instance.
     *
     * @param  ?array $notAllowedCharacter
     * @return void
     */
    public function __construct(?array $notAllowedCharacters = [])
    {

        if (!is_null($notAllowedCharacters) && count($notAllowedCharacters) > 0)
            $this->notAllowedCharacters = $notAllowedCharacters;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->notAllowedCharacters as $char) {

            if (Str::of((string) $value)->contains($char))
                $fail('validation.custom.String.NotAllowedCharacter')
                    ->translate(['notAllowedCharacters' => implode(" ", $this->notAllowedCharacters)]);
        }
    }
}
