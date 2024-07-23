<?php

namespace App\Models\General;

use App\Enums\Database\Tables\CustomizedPagesTableEnum as TableEnum;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class CustomizedPage extends SuperModel
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
        $this->timestamps = false;

        $this->fillable = [
            TableEnum::Route->dbName(),
            TableEnum::UserId->dbName(),
            TableEnum::SelectedColumns->dbName(),
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

        static::creating(function (CustomizedPage $customizedPage) {

            $customizedPage[TableEnum::Id->dbName()] = Str::orderedUuid()->toString();
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Interact with the CustomizedPage's selectedColumns.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function selectedColumns(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value)    => empty($value) ? [] : json_decode($value, true),
            set: fn (?string $value)    => empty($value) ? null : $value,
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
