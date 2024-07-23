<?php

namespace App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Models;


use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Routes\AdminRoutesEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ClientSwarmModelEnum as TableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\GendersEnum;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetconstructSwarmClient extends SuperModel
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
        $this->fillable(TableEnum::fillableApi());

        parent::__construct($attributes);
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the user that owns the BetconstructClient
     *
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get the "userBetconstruct" that owns the BetconstructClient
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userBetconstruct(): BelongsTo
    {
        return $this->belongsTo(UserBetconstruct::class, TableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/

    /**
     * Get date attributes
     *
     * @param  ?string $UTC_DateTime
     * @param  bool $onlyDate
     * @param  ?string $format
     * @return mixed
     */
    private function getDate(?string $UTC_DateTime, bool $onlyDate = false, ?string $format = null): mixed
    {
        if (empty($UTC_DateTime)) return null;

        $user = User::authUser();

        if (is_null($user)) {

            $date = Carbon::parse($UTC_DateTime);

            if (!is_null($format))
                return $date->format($format);

            if ($onlyDate)
                return $date->toDateString();
            else
                return $date->toDateTimeString();
        } else
            return $user->convertUTCToLocalTime($UTC_DateTime, $onlyDate, $format);
    }
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Interact with the client's Email.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function email(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->maskGlobalAttribute($value, AdminRoutesEnum::Global_ViewClientEmail),
        );
    }

    /**
     * Interact with the client's Email.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function phone(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->maskGlobalAttribute($value, AdminRoutesEnum::Global_ViewClientPhone),
        );
    }

    /**
     * Interact with the client's Email.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function mobilePhone(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->maskGlobalAttribute($value, AdminRoutesEnum::Global_ViewClientPhone),
        );
    }

    /**
     * Set gender attribute
     *
     * @param  mixed $value
     * @return void
     */
    public function setGenderAttribute(mixed $value): void
    {
        $this->attributes[TableEnum::Gender->dbName()] = is_null($value) ? GendersEnum::Unknown->value : $value;
    }

    /**
     * Get gender attribute
     *
     * @param  mixed $value
     * @return mixed
     */
    public function getGenderAttribute(mixed $value): mixed
    {
        return is_null($value) ? GendersEnum::Unknown->value : $value;
    }

    /**
     * Get birth_date_stamp attribute
     *
     * @param  mixed $value
     * @return mixed
     */
    public function getBirthDateAttribute(mixed $value): mixed
    {
        return $this->getDate($value, true);
    }

    /**
     * Get last_login_date attribute
     *
     * @param  mixed $value
     * @return mixed
     */
    public function getLastLoginDateAttribute(mixed $value): mixed
    {
        return $this->getDate($value, true);
    }

    /**
     * Get reg_date attribute
     *
     * @param  mixed $value
     * @return mixed
     */
    public function getRegDateAttribute(mixed $value): mixed
    {
        return $this->getDate($value, true);
    }
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
