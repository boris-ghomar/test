<?php

namespace App\Rules\BackOffice\PostGrouping;

use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\Models\BackOffice\PostGrouping\PostCategory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PostCategoryHasSpace implements ValidationRule
{
    use AddAttributesPad;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($postCategory = PostCategory::select(TableEnum::Id->dbName(), TableEnum::Title->dbName())->find($value)) {

            $name = $this->addPadToString($postCategory[TableEnum::Title->dbName()]);

            if ($postCategory->postSapces()->exists())
                $fail('thisApp.Errors.PostGrouping.PostCategoryHasSpace')->translate(['name' => $name]);
        } else
            $fail('validation.custom.notExist')->translate(['attribute' => __('validation.custom.attributes.selectedItem')]);
    }
}
