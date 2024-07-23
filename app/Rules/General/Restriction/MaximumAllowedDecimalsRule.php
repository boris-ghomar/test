<?php

namespace App\Rules\General\Restriction;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaximumAllowedDecimalsRule implements ValidationRule
{
    private $maxAllowedDecimals;

    /**
     * Create a new rule instance.
     *
     * @param int $maxAllowedDecimals
     * @return void
     */
    public function __construct(int $maxAllowedDecimals = 2)
    {
        $this->maxAllowedDecimals = $maxAllowedDecimals < 1 ? 1 : (int) $maxAllowedDecimals;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = sprintf('/^[-+]?\d+(\.\d{1,%s})?$/', $this->maxAllowedDecimals);;

        if (!preg_match($pattern, $value))
            $fail('validation.custom.number.MaximumAllowedDecimals')->translate(['maxAllowedDecimals' => $this->maxAllowedDecimals]);
    }
}
