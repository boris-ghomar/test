<?php

namespace App\Models\BackOffice\Settings;

use App\Enums\Database\Tables\TechnicalSettingsTableEnum as TableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\HHH_Library\general\php\FileAssistant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class TechnicalSetting extends Model
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
        $this->fillable = [
            TableEnum::Name->dbName(),
            TableEnum::Value->dbName(),
            TableEnum::Cast->dbName(),
        ];

        parent::__construct($attributes);
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    //
    /**************** Relationships END ********************/

    /**************** static functions ********************/

    /**
     * Get value of setting
     *
     * @param \App\Enums\Settings\AppTechnicalSettingsEnum $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(AppTechnicalSettingsEnum $key, mixed $default = null): mixed
    {
        $item = self::getItemFullRecord($key);

        if (is_null($item))
            return $default;
        else {
            $value = $item[TableEnum::Value->dbName()];

            if (is_null($value))
                return $default;

            $value = in_array($key, AppTechnicalSettingsEnum::cryptCases()) ? Crypt::decrypt($value) : $value;

            return is_null($value) ? $default : $key->cast($value);
        }
    }

    /**
     * Get complete item record information.
     *
     * @param \App\Enums\Settings\AppTechnicalSettingsEnum $key
     * @return self|null
     */
    public static function getItemFullRecord(AppTechnicalSettingsEnum $key): self|null
    {
        $item = self::where(TableEnum::Name->dbName(), $key->name)->first();

        if (is_null($item)) {

            // Try to add not exists item
            self::set($key, $key->defaultValue(false));

            /**
             * To avoid an infinite loop, the recursion function is not used
             * in cases where the input value is not correct.
             */
            $item = self::where(TableEnum::Name->dbName(), $key->name)->first();
        }

        return $item;
    }

    /**
     * Set a value for setting
     *
     * @param \App\Enums\Settings\AppTechnicalSettingsEnum $key
     * @param mixed $value
     * @return mixed
     */
    public static function set(AppTechnicalSettingsEnum $key, mixed $value): mixed
    {
        $value = $key->cast($value);
        $dbValue = in_array($key, AppTechnicalSettingsEnum::cryptCases()) ? Crypt::encrypt($value) : $value;

        if (!self::isValidValue($key, $value))
            return null;

        if (self::itemExists($key)) {

            $setting = self::where(TableEnum::Name->dbName(), $key->name)->first();

            if (!is_null($setting)) {

                if ($value !== self::get($key)) {
                    // update if value changed

                    return $setting->update([
                        TableEnum::Value->dbName()  => $dbValue,
                    ]) ? $value : null;
                } else
                    return $value;
            }
        } else {

            return self::create([
                TableEnum::Name->dbName()   => $key->name,
                TableEnum::Value->dbName()  => $dbValue,
                TableEnum::Cast->dbName()   => $key->castType()->name,
            ]) ? $value : null;
        }

        return null;
    }

    /**
     * Delete item from setting in database
     *
     * @param \App\Enums\Settings\AppTechnicalSettingsEnum $key
     * @return bool
     */
    public static function deleteItem(AppTechnicalSettingsEnum $key): bool
    {
        if ($item = self::getItemFullRecord($key)) {
            return $item->delete();
        }

        return false;
    }

    /**
     * Check if setting exists
     *
     * @param \App\Enums\Settings\AppTechnicalSettingsEnum $key
     * @return bool
     */
    public static function itemExists(AppTechnicalSettingsEnum $key): bool
    {
        $item = self::where(TableEnum::Name->dbName(), $key->name)->first();

        return is_null($item) ? false : true;
    }

    /**
     * This function validates the input value.
     *
     * @param \App\Enums\Settings\AppTechnicalSettingsEnum $key
     * @param mixed $value
     * @return bool
     */
    private static function isValidValue(AppTechnicalSettingsEnum $key, mixed $value): bool
    {

        if (!AppTechnicalSettingsEnum::hasName($key->name))
            return false;

        $validator = Validator::make(
            [$key->name => $value],
            [$key->name => $key->validationRules()]
        );

        return !$validator->fails();
    }



    /**
     * Get all settings as "$key => $value" format.
     * Used in controller
     *
     * @param boolean $outputArray  ? output = array : object;
     * @return array|object
     */
    public static function getAllSettingsValues(bool $outputArray = false): array|object
    {
        $settingsArray = [];

        foreach (AppTechnicalSettingsEnum::cases() as $case) {

            $settingsArray[$case->name] = self::get($case);
        }

        return json_decode(json_encode($settingsArray), $outputArray);
    }


    /**
     * Update all input items in database
     * Used in controller
     *
     * @param  array $inputItems
     * @param  array $fillableItems
     * @return array
     */
    public static function updateItems(array $inputItems, array $fillableItems = null): array
    {
        if (is_null($fillableItems))
            $fillableItems = AppTechnicalSettingsEnum::names();

        $lastSettings = (array) self::getAllSettingsValues(true);

        foreach ($inputItems as $key => $value) {

            if (in_array($key, $fillableItems) && AppTechnicalSettingsEnum::hasName($key)) {

                if ($value != $lastSettings[$key]) {
                    // update if value changed

                    self::set(AppTechnicalSettingsEnum::getCase($key), $value);
                }
            }
        }

        return (array) self::getAllSettingsValues(true);
    }

    /**************** static functions END ********************/
    /**************** non-static functions ********************/

    /**
     * Get photo file config
     *
     * @return \App\Enums\Resources\ImageConfigEnum
     */
    public function getPhotoFileConfig(): ImageConfigEnum
    {
        $fileConfig = ImageConfigEnum::getCase($this[TableEnum::Name->dbName()]);

        return is_null($fileConfig) ? ImageConfigEnum::Default : $fileConfig;
    }

    /**
     * Get photo file assistant
     *
     * @param bool $useFallbackPhoto : true => If the file does not exist, the return image will be the one defined in the "hhh_config" configuration
     * @return \App\HHH_Library\general\php\FileAssistant
     */
    public function getPhotoFileAssistant(bool $useFallbackPhoto = true): FileAssistant
    {
        $fileConfig = $this->getPhotoFileConfig();

        $photoEnum = (constant(AppTechnicalSettingsEnum::class . '::' . $this[TableEnum::Name->dbName()]));
        $fileAssistant = new FileAssistant($fileConfig, self::get($photoEnum));

        if ($useFallbackPhoto && !$fileAssistant->isFileExists()) {

            $fileAssistant->setPath($fileConfig->defaultPath());
            $fileAssistant->setName($fileConfig->defaultImage());
        }

        return $fileAssistant;
    }
    /**************** non-static functions END ********************/
}
