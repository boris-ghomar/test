<?php

namespace App\Rules\General\Protection;

use App\HHH_Library\general\php\traits\AddAttributesPad;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ReservedNameDelete implements ValidationRule
{
    use AddAttributesPad;
    protected array $reservedNames;

    /**
     * Create a new rule instance.
     *
     * Example:
     *  $reservedNames = config('hhh_config.reserved.workgroups');
     *
     * @param array $reservedNames such as config('hhh_config.reserved.workgroups')
     * @return void
     */
    public function __construct(array $reservedNames)
    {
        $this->reservedNames = $reservedNames;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        foreach ($this->reservedNames as $reserved) {

            if (strtolower($reserved) == strtolower($value)) {
                $fail('validation.custom.deleteBlocked')->translate(['name' => $this->addPadToString($value)]);
            }
        }
    }
}
