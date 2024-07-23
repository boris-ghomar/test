<?php

namespace App\HHH_Library\general\php\traits;

use App\Models\BackOffice\Translation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

trait  TranslateDatabaseField
{


    /**
     * This function receives a text
     * and translates it if there is a translation.
     *
     * @param  string $text
     * @return string
     */
    public static function transField($text)
    {
        if ($text == null || Str::of($text)->trim()->isEmpty())
            return $text;

        $trans = translation::where('text', $text)
            ->where('locale', App::getlocale())
            ->where('is_active', 1)
            ->whereNotNull('translation');

        if ($trans->exists()) {
            return $trans->first()->translation;
        }

        return $text;
    }

    /**
     * This function is used to translate the fields in drop boxes.
     *
     * This function receives an array as input
     * and an array as the keys we need to translate,
     * and translates the items of those keys if any.
     *
     * Examples:
     * $items: [
     *              ["id" => -1, "name" => ""],
     *              ["id" => 1, "name" => "developer"],
     *              ["id" => 2, "name" => "broker"],
     *         ]
     * $reqKeys = ['name'];
     *
     * $res: "name" items will be translate;
     *
     * @param array $items
     * @param array $reqKeys
     * @return array
     */
    public static function transDropboxFields(array $items, array $reqKeys)
    {

        $res = [];

        foreach ($items as $item) {

            foreach ($item as $key => $value) {

                if (in_array($key, $reqKeys)) {

                    $item[$key] = self::transField($value);
                }
            }
            array_push($res, $item);
        }

        return $res;
    }

    /**
     * This function takes an object from the eloquent model
     * and translates its translatable fields.
     *
     * @param [Model Object] $eloquentModelObject
     * @return [Model Object] translatd object
     */
    public static function transTableRecord($eloquentModelObject)
    {

        $tableName = $eloquentModelObject->getTable();

        $translatableFields = config('hhh_config.TranslatableColumnsOfTables.' . $tableName);

        foreach ($translatableFields as $field) {
            $eloquentModelObject->$field = self::transField($eloquentModelObject->$field);
        }

        return $eloquentModelObject;
    }
}
