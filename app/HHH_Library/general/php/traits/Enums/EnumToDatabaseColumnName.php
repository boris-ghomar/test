<?php

namespace App\HHH_Library\general\php\traits\Enums;

use App\Enums\Database\DatabaseTablesEnum;
use Illuminate\Support\Str;

trait  EnumToDatabaseColumnName
{

    /**
     * Convert name or value of case to database column name lowercase or snake
     *
     * @param  bool $returnCaseName : true = case->value, false: case->name
     * @return string
     */
    public function dbName(bool $returnCaseName = true): string
    {
        $res = $returnCaseName ? $this->name : $this->value;

        if ($res === Str::of($res)->upper()->toString())
            return Str::of($res)->lower()->toString();
        else
            return Str::of($res)->snake()->toString();
    }

    /**
     * Convert name or value of case to database column name lowercase or snake
     * With table name in database
     *
     * @param  \App\Enums\Database\DatabaseTablesEnum $tableName
     * @param  bool $useTablePrefix
     * @param  bool $returnCaseName : true = case->value, false: case->name
     * @return string
     */
    public function dbNameWithTable(DatabaseTablesEnum $tableName, bool $useTablePrefix = false, bool $returnCaseName = true): string
    {
        return $tableName->tableName($useTablePrefix) . '.' . $this->dbName($returnCaseName);
    }


    /**
     * Convert name or value of case to database foreign key id
     *
     * Exampel::
     * column: foreignId(role_id) in users table => gwz2xa_users_role_id_foreign
     *
     * @param  \App\Enums\Database\DatabaseTablesEnum $tableName
     * @param  bool $useTablePrefix
     * @param  bool $returnCaseName : true = case->value, false: case->name
     * @return string
     */
    public function dbForeignId(DatabaseTablesEnum $tableName, bool $useTablePrefix = true, bool $returnCaseName = true): string
    {
        return $tableName->tableName($useTablePrefix) . '_' . $this->dbName($returnCaseName) . '_foreign';
    }
}
