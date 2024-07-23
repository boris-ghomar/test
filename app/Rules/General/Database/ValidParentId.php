<?php

namespace App\Rules\General\Database;

use App\HHH_Library\general\php\traits\AddAttributesPad;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidParentId implements ValidationRule
{
    use AddAttributesPad;

    private $thisId;
    private $model;
    private $columnId;
    private $withoutParentValue;

    /**
     * Create a new rule instance.
     *
     * @param  mixed $thisId If it is a new record send null
     * @param  string $model model::class
     * @param  string $columnId The name of column id (primary key)
     * @param  string|int $withoutParentValue The value that used for without parent_id records
     * @return void
     */
    public function __construct(mixed $thisId = null, string $model, string $columnId = 'id', string|int $withoutParentValue = 0)
    {
        $this->thisId = $thisId;
        $this->model = $model;
        $this->columnId = $columnId;
        $this->withoutParentValue = $withoutParentValue;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value != $this->withoutParentValue) {

            if ($value === $this->thisId)
                $fail('validation.custom.ParentId.sameIdAndParentId')->translate();

            $parent = $this->model::where($this->columnId, $value)->exists();

            if (!$parent)
                $fail('validation.custom.notExist')->translate();
        }
    }
}
