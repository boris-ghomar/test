<?php

namespace App\Models\BackOffice\Domains;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;

class DomainImport extends Domain
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
        $this->table = DatabaseTablesEnum::Domains->tableName();

        parent::__construct($attributes);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    //
    /**************** Relationships END ********************/

    /**************** Exclusive Items ********************/
    //
    /**************** Exclusive Items END ********************/

    /**************** Accessors & Mutators ********************/
    //
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    //
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    //
    /**************** scopes END ********************/
}
