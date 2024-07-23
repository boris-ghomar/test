<?php

namespace App\Rules\General\User;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveUser implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $statusCol = UsersTableEnum::Status->dbName();

        $user = User::find($value);

        if (is_null($user))
            $fail('thisApp.Errors.rules.IsUserActive')->translate();

        if ($user->$statusCol !== UsersStatusEnum::Active->name)
            $fail('thisApp.Errors.rules.IsUserActive')->translate();

    }
}
