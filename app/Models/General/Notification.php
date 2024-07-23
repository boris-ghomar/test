<?php

namespace App\Models\General;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\NotificationsTableEnum as TableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\HHH_Library\general\php\traits\ModelSuperScopes;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{

    use ModelSuperScopes;
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
            TableEnum::Type->dbName(),
            TableEnum::NotifiableType->dbName(),
            TableEnum::NotifiableId->dbName(),
            TableEnum::Data->dbName(),
            TableEnum::ReadAt->dbName(),
        ];

        parent::__construct($attributes);
    }

    /**
     * The "booted" method of the model.
     * This scope controls only personnel users to be loaded on all requests of this model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(ModelGlobalScopesEnum::Notification_Notifiable->name, function (Builder $builder) {

            $userId = auth()->check()  ? auth()->user()->id : null;

            $builder->where(TableEnum::NotifiableId->dbName(), $userId);
        });
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
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
        return $query;
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, ?array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
                ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'desc')
                ->orderBy(TableEnum::Id->dbName(), 'desc');
        }, $replaceSortFields);
    }

    /**************** scopes END ********************/
}
