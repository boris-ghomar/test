<?php

namespace App\Rules\General\User;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PersonnelUser implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        /** @var User $user */
        $user = User::find($value);

        if (is_null($user))
            $fail('thisApp.Errors.rules.IsUserActive')->translate();

        if (!$user->isPersonnel())
            $fail('thisApp.Errors.rules.IsUserActive')->translate();
    }
}
