<?php

namespace App\Models\BackOffice\Tickets;

use App\Enums\Tickets\TicketableTypesEnum;
use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\TicketMessagesTableEnum;
use App\Enums\Database\Tables\TicketsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends SuperModel
{
    use HasFactory;
    use SoftDeletes;

    /**************** Parnet Items ********************/

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {

        $this->fillable = [
            TableEnum::OwnerId->dbName(),
            TableEnum::TicketableType->dbName(),
            TableEnum::TicketableId->dbName(),
            TableEnum::Subject->dbName(),
            TableEnum::Priority->dbName(),
            TableEnum::Status->dbName(),
            TableEnum::PrivateNote->dbName(),
        ];

        parent::__construct($attributes);

        $this->append([
            'AnsweringUrl'
        ]);
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the Owner(Client) that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::OwnerId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get the Responder(Personnel) that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::ResponderId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get the Ticketable that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function ticketable(): ?BelongsTo
    {
        $ticketableType = TicketableTypesEnum::getCase($this[TableEnum::TicketableType->dbName()]);

        if (!is_null($ticketableType)) {

            $ticketableModelClass = $ticketableType->getTicketableClass();
            return $this->belongsTo($ticketableModelClass, TableEnum::TicketableId->dbName(), 'id');
        }

        return null;
    }

    /**
     * Get all of the ticketMessages for the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ticketMessages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, TicketMessagesTableEnum::TicketId->dbName(), TableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/

    /**
     * Get last update time
     *
     * @param  bool $duration
     * @return string
     */
    public function getLastUpdate(): string
    {
        return $this[TimestampsEnum::UpdatedAt->dbName()];
    }

    /**
     * Get last update time form support
     *
     * @return ?string
     */
    public function getSupportLastUpdate(): ?string
    {
        $supportLastMessage = $this->ticketMessages()
            ->where(TicketMessagesTableEnum::UserId->dbName(), "!=", $this[TableEnum::OwnerId->dbName()])
            ->orderBy(TimestampsEnum::UpdatedAt->dbName(), 'desc')
            ->first();

        return is_null($supportLastMessage) ? null : $supportLastMessage[TimestampsEnum::UpdatedAt->dbName()];
    }
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * All times are stored in the database based on UTC time(00:00),
     * so time is converted to user local timezone,
     * and selected calendar type
     * based on the user and admin panel settings.
     *
     * @param  mixed $value
     * @return ?string
     */
    public function getCreatedAtAttribute(mixed $value): ?string
    {
        $user = User::authUser();

        return is_null($user) ? $value : $user->convertUTCToLocalTime($value);
    }

    /**
     * All times are stored in the database based on UTC time(00:00),
     * so time is converted to user local timezone,
     * and selected calendar type
     * based on the user and admin panel settings.
     *
     * @param  mixed $value
     * @return ?string
     */
    public function getUpdatedAtAttribute(mixed $value): ?string
    {
        $user = User::authUser();

        return is_null($user) ? $value : $user->convertUTCToLocalTime($value);
    }

    /**
     * Get the chat answering URL (Chat page with client)
     *
     * @return string
     */
    public function getAnsweringUrlAttribute(): string
    {
        return AdminPublicRoutesEnum::Ticket_Messenger->url(['ticket' => $this[TableEnum::Id->dbName()]]);
    }
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Scope a collection of scopes for get all items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->Id($filter)
            ->OwnerId($filter)
            ->Priority($filter)
            ->Subject($filter)
            ->Status($filter)
            ->CreatedAt($filter)
            ->PrivateNote($filter);
    }

    /**
     * Scope a collection of scopes for get all items for using in controller list.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeControllerAllItems(Builder $query, array $filter): Builder
    {
        return $query->AllItems($filter)
            ->BetconstructId($filter)
            ->BetconstructUsername($filter)
            ->ClientCategory($filter)
            ->PersonnelUsername($filter);
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
        $ticketsTable = DatabaseTablesEnum::Tickets;
        $clientExtrasTable = DatabaseTablesEnum::BetconstructClients;
        $usersTable = DatabaseTablesEnum::Users;
        $rolesTable = DatabaseTablesEnum::Roles;

        return $query
            ->ControllerAllItems($filter)
            ->leftJoin($clientExtrasTable->tableName(), ClientModelEnum::UserId->dbNameWithTable($clientExtrasTable), '=', TableEnum::OwnerId->dbNameWithTable($ticketsTable))
            ->leftJoin($usersTable->tableName() . ' as client', 'client.id', '=', TableEnum::OwnerId->dbNameWithTable($ticketsTable))
            ->leftJoin($rolesTable->tableName() . ' as client_category', 'client_category.id', '=', 'client.' . UsersTableEnum::RoleId->dbName())
            ->leftJoin($usersTable->tableName() . ' as personnel', 'personnel.id', '=', TableEnum::ResponderId->dbNameWithTable($ticketsTable))
            ->select(
                $ticketsTable->tableName() . '.*',

                'client.' . UsersTableEnum::RoleId->dbName() .' as client_category_id',

                'client_category.'.RolesTableEnum::Name->dbName() . ' as client_category_name',

                'personnel.'.UsersTableEnum::Username->dbName() . ' as personnel_username',

                ClientModelEnum::Id->dbNameWithTable($clientExtrasTable) . ' as betconstruct_id',
                ClientModelEnum::Login->dbNameWithTable($clientExtrasTable) . ' as betconstruct_username',
            )
            ->SortOrder($filter, [
                'client_category_id'  => ' client_category_name',
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
                ->orderBy(TableEnum::Status->dbName(), 'asc')
                ->orderBy(TableEnum::Priority->dbName(), 'asc')
                ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'asc');
        }, $replaceSortFields);
    }

    /**
     * Scope a query to only include "id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::Id->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "owner_id" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwnerId(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeExactlyNumber(TableEnum::OwnerId->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "priority" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePriority(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropbox(TableEnum::Priority->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "subject" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubject(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Subject->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "status" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeDropbox(TableEnum::Status->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "created_at" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedAt(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeDateRange(TimestampsEnum::CreatedAt->dbName(), $query, $filter, null, User::authUser()->getCalendarHelper());
    }

    /**
     * Scope a query to only include "private_note" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrivateNote(Builder $query, ?array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::PrivateNote->dbName(), $query, $filter);
    }

    /**************** Joined Items ********************/

    /**
     * Scope a query to only include "betconstruct_id" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetconstructId(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'betconstruct_id';

        $dbCol = ClientModelEnum::Id->dbNameWithTable(DatabaseTablesEnum::BetconstructClients);

        return $this->superScopeExactlyNumber($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "login" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetconstructUsername(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'betconstruct_username';
        $dbCol = ClientModelEnum::Login->dbNameWithTable(DatabaseTablesEnum::BetconstructClients);

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "role_id" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClientCategory(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'client_category_id';
        $dbCol = 'client.' . UsersTableEnum::RoleId->dbName();

        return $this->superScopeDropboxId($dbCol, $query, $filter, $filterKey);
    }

    /**
     * Scope a query to only include "role_id" as request.
     * login is username in betconstruct model
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePersonnelUsername(Builder $query, ?array $filter = null): Builder
    {
        $filterKey = 'personnel_username';
        $dbCol = 'personnel.' . UsersTableEnum::Username->dbName();

        return $this->superScopeLikeAs($dbCol, $query, $filter, $filterKey);
    }
    /**************** Joined Items END ********************/

    /**************** scopes END ********************/
}
