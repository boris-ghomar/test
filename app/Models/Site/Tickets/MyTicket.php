<?php

namespace App\Models\Site\Tickets;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\TicketsTableEnum as TableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Models\BackOffice\Tickets\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyTicket extends Ticket
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

        $this->fillable = [];
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

        static::addGlobalScope(ModelGlobalScopesEnum::MyTicket_Only->name, function (Builder $builder) {

            $builder->where(TableEnum::OwnerId->dbName(), User::authUser()->id);
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
