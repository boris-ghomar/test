<?php

namespace App\Rules\BackOffice\Role;

use App\Enums\Database\Tables\RolesTableEnum;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\Models\General\Role;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HasUser implements ValidationRule
{
    use AddAttributesPad;


    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if ($role = Role::select(RolesTableEnum::Id->dbName(), RolesTableEnum::Name->dbName())->find($value)) {

            $name = $this->addPadToString($role[RolesTableEnum::Name->dbName()]);

            if ($role->users()->exists())
                $fail('thisApp.Errors.Rules.HasUser')->translate(['name' => $name]);
        } else
            $fail('validation.custom.notExist')->translate(['attribute' => __('validation.custom.attributes.selectedItem')]);
    }
}
