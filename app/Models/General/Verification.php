<?php

namespace App\Models\General;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Database\Tables\VerificationsTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Models\SuperClasses\SuperModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Verification extends SuperModel
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

        $this->keyType = 'string';

        $this->incrementing = false;

        $this->fillable = [
            TableEnum::UserId->dbName(),
            TableEnum::Type->dbName(),
            TableEnum::OldValue->dbName(),
            TableEnum::NewValue->dbName(),
            TableEnum::ValidUntil->dbName(),
        ];

        $this->attributes = [
            TableEnum::IsVerified->dbName() => 0,
        ];

        $this->casts = [
            TableEnum::IsVerified->dbName() => 'boolean',
        ];

        parent::__construct($attributes);
    }

    /**
     * The "booted" method of the model.
     * This scope controls only personnel users to be loaded on all requests of this model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (Verification $verification) {

            $verification[TableEnum::Id->dbName()] = Str::orderedUuid()->toString();
            $verification[TableEnum::Code->dbName()] = rand(100000, 999999);
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the user that owns the Verification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    //
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Interact with the verification's IsVerified.
     *
     * Convert string boolean to boolean when saving information.
     * When receiving information from the API client,
     * boolean values may be received in the form of strings.
     *
     * Example:
     *   "true" => true
     *   "false" => false
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function isVerified(): Attribute
    {
        return Attribute::make(
            get: fn (bool|int|string $value)    => CastEnum::Boolean->cast($value),
            set: fn (bool|int|string $value)    => CastEnum::Boolean->cast($value),
        );
    }
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/
    //
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    //
    /**************** scopes END ********************/
}
