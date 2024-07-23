<?php

namespace App\Rules\General\Database;

use App\HHH_Library\general\php\ClassHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniqueInModel implements ValidationRule
{
    private $modelClass;
    private $thisId;
    private $column;

    /**
     * Create a new rule instance.
     *
     *
     * @param  string $modelClass
     * @param  string|int|null $thisId The ID of this record in database. Null for new record.
     * @param  ?string $column Column name in database table. null ? using attribute name
     * @return void
     */
    public function __construct(string $modelClass, string|int|null $thisId, ?string $column = null)
    {
        $this->modelClass = $modelClass;
        $this->thisId = $thisId;
        $this->column = $column;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $column = is_null($this->column) ? $attribute : $this->column;
        $primaryKey = (new $this->modelClass)->getKeyName();

        $query = new $this->modelClass();
        $query = $query->where($column, $value);


        // ignore this id
        $query->where($primaryKey, "!=", $this->thisId);

        if (ClassHelper::hasTrait($this->modelClass, SoftDeletes::class))
            $query->withTrashed();

        if ($query->count() > 0)
            $fail('validation.unique')->translate();
    }
}
