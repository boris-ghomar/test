<?php

namespace App\Rules\BackOffice\Domains\DomainHolder;

use App\Enums\Database\Tables\DomainHoldersTableEnum;
use App\HHH_Library\general\php\traits\addAttributesPad;
use App\Models\BackOffice\Domains\DomainHolder;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HasDomainHolderAccount implements ValidationRule
{
    use addAttributesPad;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $idCol = DomainHoldersTableEnum::Id->dbName();
        $nameCol = DomainHoldersTableEnum::Name->dbName();

        if ($domainHolder = DomainHolder::select($idCol, $nameCol)->find($value)) {

            if ($domainHolder->domainHolderAccount()->exists())
                $fail('thisApp.Errors.DomainHolder.hasAccount')->translate(['name' => $this->addPadToString($domainHolder->$nameCol)]);
        }
    }
}
