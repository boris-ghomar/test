<?php

namespace App\Rules\General\Protection;

use App\HHH_Library\general\php\traits\AddAttributesPad;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ReservedNameEdit implements ValidationRule
{
    use AddAttributesPad;

    protected array $reservedNames;
    protected $previousName;

    /**
     * Create a new rule instance.
     *
     * Example:
     *  $reservedNames = config('hhh_config.reserved.workgroups');
     *
     * @param array $reservedNames such as config('hhh_config.reserved.workgroups')
     * @param  string $previousName
     * @return void
     */
    public function __construct(array $reservedNames, string $previousName)
    {
        $this->reservedNames = $reservedNames;
        $this->previousName = $previousName;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        // name chanegd
        if (strtolower($value) !== strtolower($this->previousName)) {

            foreach ($this->reservedNames as $reserved) {

                // If the previous name is reserved, its change will be blocked
                if (strtolower($this->previousName) == strtolower($reserved)) {
                    $fail('validation.custom.editBlocked')->translate(['name' => $this->addPadToString($this->previousName)]);
                }

                // If the new name is reserved, its change will be blocked
                if (strtolower($value) == strtolower($reserved)) {
                    $fail('validation.custom.reservedBySystem')->translate(['name' => $this->addPadToString($value)]);
                }
            }
        }
    }
}
