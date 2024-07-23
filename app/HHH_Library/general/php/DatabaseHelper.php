<?php

namespace App\HHH_Library\general\php;

use Illuminate\Support\Facades\Schema;


class DatabaseHelper
{

    /**
     * This function checks whether the database table has the desired column.
     *
     * @param  ?string $table The name of table in database
     * @param  ?string $column
     *
     * @return bool
     */
    public static function hasColumn(?string $table, ?string $column): bool
    {
        if(empty($modelClass) || empty($column)) return false;

        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get the database table column list.
     *
     * @param  ?string $table ->Example: test::class
     * @return array
     */
    public static function getColumnList(?string $table):array
    {
        if(empty($table)) return [];

        try {
            return Schema::getColumnListing($table);
        } catch (\Throwable $th) {
            return [];
        }
    }
}
