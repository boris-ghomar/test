<?php

namespace App\Models\General;

use App\Enums\Database\Tables\ApiNewAttrinutesTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Models\SuperClasses\SuperModel;

class ApiNewAttrinute extends SuperModel
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
        $this->fillable([
            TableEnum::ClassName->dbName(),
            TableEnum::Attrinute->dbName(),
            TableEnum::Values->dbName(),
            TableEnum::Descr->dbName(),
        ]);

        $this->casts = [
            TableEnum::Values->dbName() => 'array',
        ];

        parent::__construct($attributes);
    }

    /**************** Parnet Items END ********************/

    /**************** Accessors & Mutators ********************/

    /**
     * Set values attribute
     *
     * @param  mixed $value
     * @return void
     */
    public function setValuesAttribute(mixed $value)
    {
        $this->attributes[TableEnum::Values->dbName()] = json_encode($value);
    }

    /**
     * Get values attribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getValuesAttribute(mixed $value)
    {
        return json_decode($value, true);
    }

    /**************** Accessors & Mutators END ********************/


    /**
     * Save new attribute item in database
     *
     * @param  ?string $class Exmp: __CLASS__
     * @param  ?string $attribute
     * @param  mixed $value
     * @return void
     */
    public static function saveNewItem(?string $class, ?string $attribute, mixed $value): void
    {

        if (!empty($class) && !empty($attribute)) {

            $maxSaveValues = 5;

            $value = CastEnum::String->cast($value);

            /** @var self $attribute */
            $item = self::where(TableEnum::ClassName->dbName(), $class)
                ->where(TableEnum::Attrinute->dbName(), $attribute)->first();

            if (is_null($item)) {
                // New Attribute

                $item = new ApiNewAttrinute([
                    TableEnum::ClassName->dbName() => $class,
                    TableEnum::Attrinute->dbName() => $attribute,
                    TableEnum::Values->dbName() => array($value),
                ]);
            } else {
                // Existing Attribute
                $values = $item[TableEnum::Values->dbName()];

                if (count($values) < $maxSaveValues && !in_array($value, $values)) {

                    array_push($values, $value);
                    $item[TableEnum::Values->dbName()] = $values;
                }
            }
            $item->save();
        }
    }
}
