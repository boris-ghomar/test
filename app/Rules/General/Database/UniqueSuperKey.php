<?php

namespace App\Rules\General\Database;

use App\HHH_Library\general\php\ClassHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniqueSuperKey implements ValidationRule
{
    protected $modelClass;
    protected $id;
    protected $keyColumns;

    /**
     * Create a new rule instance.
     *
     * $keyColumns :
     *  The names of the columns in the database with current item value (key => value),
     *  which must be merged to form a superkey.
     *
     *  Example:
     *  [
     *      'name'  => $this->name,
     *      'parent_id' => $this->parent_id,
     * ]
     *
     * @param  string $modelClass
     * @param  string|int|null $id
     * @param  array $keyColumns
     * @return void
     */
    public function __construct(string $modelClass, string|int|null $id, array $keyColumns)
    {
        $this->modelClass = $modelClass;
        $this->id = $id;
        $this->keyColumns = $keyColumns;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $primaryKey = (new $this->modelClass)->getKeyName();

        $query = new $this->modelClass();
        foreach ($this->keyColumns as $key => $value) {

            $query = $query->where($key, $value);
        }

        // ignore this id
        $query->where($primaryKey, "!=", $this->id);

        if (ClassHelper::hasTrait($this->modelClass, SoftDeletes::class))
            $query->withTrashed();

        if ($query->count() > 0)
            $fail('validation.unique')->translate();
    }
}
