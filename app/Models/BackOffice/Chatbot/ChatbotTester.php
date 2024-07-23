<?php

namespace App\Models\BackOffice\Chatbot;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ChatbotsTableEnum;
use App\Enums\Database\Tables\ChatbotTestersTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ChatbotTester extends SuperModel
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

        $this->table = DatabaseTablesEnum::ChatbotTesters->tableName();
        $this->keyType = 'string';
        $this->incrementing = false;
        $this->timestamps = false;

        $this->fillable = [
            TableEnum::ChatbotId->dbName(),
            TableEnum::UserId->dbName(),
        ];

        parent::__construct($attributes);
    }

    /**
     * @override parent boot
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {

            $model[TableEnum::Id->dbName()] = Str::orderedUuid();
            return $model;
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the Chatbot that owns the ChatbotTester
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chatbot(): BelongsTo
    {
        return $this->belongsTo(Chatbot::class, TableEnum::ChatbotId->dbName(), ChatbotsTableEnum::Id->dbName());
    }

    /**
     * Get the User that owns the ChatbotTester
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

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
            ->UserId($filter)
            ->ChatbotId($filter);
    }

    /**
     * Scope a collection of scopes for get all items for controller list.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeControllerAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->AllItems($filter)
            ->BcUsername($filter);
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
        $thisTable = DatabaseTablesEnum::ChatbotTesters;
        $ChatbotsTable = DatabaseTablesEnum::Chatbots;
        $betconstructClientsTable = DatabaseTablesEnum::BetconstructClients;

        return $query
            ->ControllerAllItems($filter)
            ->join($ChatbotsTable->tableName(), ChatbotsTableEnum::Id->dbNameWithTable($ChatbotsTable), '=', TableEnum::ChatbotId->dbNameWithTable($thisTable))
            ->join($betconstructClientsTable->tableName(), ClientModelEnum::UserId->dbNameWithTable($betconstructClientsTable), '=', TableEnum::UserId->dbNameWithTable($thisTable))
            ->select(

                $thisTable->dbName() . '.*',

                ChatbotsTableEnum::Name->dbNameWithTable($ChatbotsTable) . ' as chatbot_name',

                ClientModelEnum::Login->dbNameWithTable($betconstructClientsTable) . ' as bc_username',

            )
            ->SortOrder($filter, [
                TableEnum::ChatbotId->dbName() => 'chatbot_name',

            ]);
    }

    /**************** scopes Collection END ********************/

    /**************** scopes ********************/

    /**
     * Scope a query to set SortOrder as request or defults.
     *
     * @param array $replaceSortFields
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, ?array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
                ->orderBy('chatbot_name', 'asc')
                ->orderBy('bc_username', 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "user_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::UserId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "chatbot_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChatbotId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropboxId(TableEnum::ChatbotId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "bc_username" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBcUsername(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'bc_username';
        $dbCol = ClientModelEnum::Login->dbNameWithTable(DatabaseTablesEnum::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**************** scopes END ********************/
}
