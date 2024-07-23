<?php

namespace App\Rules\General\Restriction;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class AllowedCity implements ValidationRule
{

    private $provinceKey;

    /**
     * Create a new rule instance.
     *
     * @param ?string $provinceKey The key that used in the "IranCities" language file
     * @return void
     */
    public function __construct(?string $provinceKey)
    {
        $this->provinceKey = $provinceKey;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cities = trans('IranCities.Cities.' . $this->provinceKey, [], 'en');

        if (!is_array($cities))
            $fail('validation.custom.notExist')->translate();
        else {

            if (!Arr::has($cities, $value))
                $fail('validation.custom.notExist')->translate();
        }
    }
}
