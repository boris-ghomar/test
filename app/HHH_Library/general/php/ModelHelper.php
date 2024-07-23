<?php

namespace App\HHH_Library\general\php;


class ModelHelper
{

    /**
     * This function checks whether a model has the desired column in its database table
     *
     * @param  ?string $modelClass ->Example: test::class
     * @param  ?string $column
     *
     * @return bool
     */
    public static function hasColumn(?string $modelClass, ?string $column): bool
    {
        if (empty($modelClass) || empty($column)) return false;

        try {
            $model = new $modelClass();
            return DatabaseHelper::hasColumn($model->getTable(), $column);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get the model column list in the database table.
     *
     * @param  ?string $modelClass ->Example: test::class
     * @return array
     */
    public static function getColumnList(?string $modelClass): array
    {
        if (empty($modelClass)) return [];

        try {
            $model = new $modelClass();
            return DatabaseHelper::getColumnList($model->getTable());
        } catch (\Throwable $th) {
            return [];
        }
    }
}
