<?php

namespace App\Models\BackOffice\Tickets;

use App\Enums\Tickets\TicketsStatusEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\TicketsTableEnum as TableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class OpenTicket extends Ticket
{
    use HasFactory;

    /**************** Parnet Items ********************/

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = DatabaseTablesEnum::Tickets->tableName();

        parent::__construct($attributes);
    }

    /**
     * The "booted" method of the model.
     * This scope controls only Betcart users to be loaded on all requests of this model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();

        static::addGlobalScope(ModelGlobalScopesEnum::OpenTicket_Only->name, function (Builder $builder) {

            $builder->where(TableEnum::Status->dbNameWithTable(DatabaseTablesEnum::Tickets), "!=", TicketsStatusEnum::Closed->name);
        });
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/

    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
