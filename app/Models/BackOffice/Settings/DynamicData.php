<?php

namespace App\Models\BackOffice\Settings;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DynamicDatasTableEnum as TableEnum;
use App\Enums\Settings\DynamicDataVariablesEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;

class DynamicData extends SuperModel
{

    /**************** Parnet Items ********************/

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = DatabaseTablesEnum::DynamicDatas->tableName();

        parent::__construct($attributes);

        $this->fillable = [
            TableEnum::VarName->dbName(),
            TableEnum::VarValue->dbName(),
            TableEnum::Descr->dbName(),
        ];
    }

    /**
     * @override parent boot
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::saving(function (self $model) {

            $varNameCol = TableEnum::VarName->dbName();
            $varValueCol = TableEnum::VarValue->dbName();

            /** @var  DynamicDataVariablesEnum $itemCase */
            $itemCase = DynamicDataVariablesEnum::getCase($model[$varNameCol]);

            if (!is_null($itemCase)) {

                $model[$varValueCol] = $itemCase->modifyValue($model[$varValueCol]);
            }

            return $model;
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**************** Relationships END ********************/

    /**************** static functions ********************/
    /**
     * Check if dynamic data exists
     *
     * @param \App\Enums\Settings\DynamicDataVariablesEnum $key
     * @return bool
     */
    public static function itemExists(DynamicDataVariablesEnum $key): bool
    {
        $item = self::where(TableEnum::VarName->dbName(), $key->name)->first();

        return is_null($item) ? false : true;
    }

    /**
     * Create or update a dynamic data record.
     *
     * @param \App\Enums\Settings\DynamicDataVariablesEnum $key
     * @param  ?string $value
     * @return self
     */
    public static function set(DynamicDataVariablesEnum $key, ?string $value): self
    {
        if (self::itemExists($key)) {
            $model = self::where(TableEnum::VarName->dbName(), $key->name)->first();
        } else
            $model = new self();

        $model->fill([
            TableEnum::VarName->dbName()   => $key->name,
            TableEnum::VarValue->dbName()  => $key->modifyValue($value),
        ]);

        $model->save();

        return $model;
    }

    /**
     * Get value of dynamic data record
     *
     * @param \App\Enums\Settings\DynamicDataVariablesEnum $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(DynamicDataVariablesEnum $key, mixed $default = null): mixed
    {
        $item = self::where(TableEnum::VarName->dbName(), $key->name)->first();

        if (is_null($item))
            return $default;

        $value = $item[TableEnum::VarValue->dbName()];

        return is_null($value) ? $default : $value;
    }

    /**************** static functions END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Scope a collection of scopes for get all items.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->VarName($filter)
            ->VarValue($filter)
            ->Description($filter);
    }

    /**
     * Scope a collection of scopes for the "Controller->apiIndex" function.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApiIndexCollection(Builder $query, array $filter): Builder
    {
        return $query
            ->AllItems($filter)
            ->SortOrder($filter);
    }
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/

    /**
     * Scope a query to set SortOrder as request or defults.
     *
     * @param array $replaceSortFields
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  array $replaceSortFields
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
                ->orderBy(TableEnum::VarName->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "var_name" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVarName(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::VarName->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "var_value" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVarValue(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDropbox(TableEnum::VarValue->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "descr" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescription(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Descr->dbName(), $query, $filter);
    }
    /**************** scopes END ********************/
}
