<?php

namespace App\Models\Site\Chatbot;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ChatbotChatsTableEnum as TableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Models\BackOffice\Chatbot\ChatbotChat;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyChatbotChat extends ChatbotChat
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
        $this->table = DatabaseTablesEnum::ChatbotChats->tableName();

        parent::__construct($attributes);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();

        static::addGlobalScope(ModelGlobalScopesEnum::MyChatbotChat_Only->name, function (Builder $builder) {

            $builder->where(TableEnum::UserId->dbName(), User::authUser()->id);
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
