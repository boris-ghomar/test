<?php

namespace App\Models\BackOffice\Settings;

use App\Enums\Database\Tables\SettingsTableEnum as TableEnum;
use App\Enums\Database\Tables\UserSettingsTableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\Models\General\UserSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Validator;

class Setting extends Model
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

    /**
     * Get all of the UserSetting for the Setting
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userSettings(): HasMany
    {
        return $this->hasMany(UserSetting::class, UserSettingsTableEnum::SettingId->dbName())
            ->withoutGlobalScope(ModelGlobalScopesEnum::UserSetting_UserPersonalSetting->name);
    }

    /**
     * Get the UserSetting associated with the Setting
     *
     * This relationship is used to receive a setting for a user,
     * and these settings are unique per user ID and setting ID.
     *
     * Used in "App\Models\General\UserSetting" model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userSetting(): HasOne
    {
        return $this->hasOne(UserSetting::class, UserSettingsTableEnum::SettingId->dbName());
    }
    /**************** Relationships END ********************/

    /**************** static functions ********************/

    /**
     * Add a settings value.
     *
     * @param \App\Enums\Settings\AppSettingsEnum $key
     * @param  ?string $value
     * @return ?string
     */
    public static function add(AppSettingsEnum $key, ?string $value): ?string
    {

        if (!self::isValidValue($key, $value))
            return null;

        if (self::itemExists($key)) {

            return self::set($key, $value);
        }

        return self::create([
            TableEnum::Name->dbName()   => $key->name,
            TableEnum::Value->dbName()  => $value,
            TableEnum::Cast->dbName()   => $key->castType()->name,
        ]) ? $value : null;
    }

    /**
     * Get value of setting
     *
     * @param \App\Enums\Settings\AppSettingsEnum $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(AppSettingsEnum $key, mixed $default = null): mixed
    {
        $item = self::getItemFullRecord($key);

        if (is_null($item))
            return $default;

        $value = $item[TableEnum::Value->dbName()];

        return is_null($value) ? $default : $key->cast($value);
    }

    /**
     * Get complete item record information.
     *
     * @param \App\Enums\Settings\AppSettingsEnum $key
     * @return self|null
     */
    public static function getItemFullRecord(AppSettingsEnum $key): self|null
    {
        $item = self::where(TableEnum::Name->dbName(), $key->name)->first();

        if (is_null($item)) {

            self::add($key, $key->defaultValue(false));

            return self::getItemFullRecord($key);
        }

        return $item;
    }

    /**
     * Set a value for setting
     *
     * @param \App\Enums\Settings\AppSettingsEnum $key
     * @param mixed $value
     * @return mixed
     */
    public static function set(AppSettingsEnum $key, mixed $value): mixed
    {
        $value = $key->cast($value);

        if (!self::isValidValue($key, $value))
            return null;

        if ($setting = self::getItemFullRecord($key)) {

            if ($value !== self::get($key)) {
                // update if value changed

                return $setting->update([
                    TableEnum::Value->dbName()  => $value,
                ]) ? $value : null;
            }
        }

        return null;
    }


    /**
     * Delete item from setting in database
     *
     * @param \App\Enums\Settings\AppSettingsEnum $key
     * @return bool
     */
    public static function deleteItem(AppSettingsEnum $key): bool
    {
        if ($item = self::getItemFullRecord($key)) {
            return $item->delete();
        }

        return false;
    }

    /**
     * Check if setting exists
     *
     * @param \App\Enums\Settings\AppSettingsEnum $key
     * @return bool
     */
    public static function itemExists(AppSettingsEnum $key): bool
    {
        $item = self::where(TableEnum::Name->dbName(), $key->name)->first();

        return is_null($item) ? false : true;
    }

    /**
     * This function validates the input value.
     *
     * @param \App\Enums\Settings\AppSettingsEnum $key
     * @param mixed $value
     * @return bool
     */
    private static function isValidValue(AppSettingsEnum $key, mixed $value): bool
    {

        if (!AppSettingsEnum::hasName($key->name))
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

        foreach (AppSettingsEnum::cases() as $case) {

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
            $fillableItems = AppSettingsEnum::names();

        $lastSettings = (array) self::getAllSettingsValues(true);

        foreach ($inputItems as $key => $value) {

            if (in_array($key, $fillableItems) && AppSettingsEnum::hasName($key)) {

                if ($value !== $lastSettings[$key]) {
                    // update if value changed

                    self::set(AppSettingsEnum::getCase($key), $value);
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

        $photoEnum = (constant(AppSettingsEnum::class . '::' . $this[TableEnum::Name->dbName()]));
        $fileAssistant = new FileAssistant($fileConfig, self::get($photoEnum));

        if ($useFallbackPhoto && !$fileAssistant->isFileExists()) {

            $fileAssistant->setPath($fileConfig->defaultPath());
            $fileAssistant->setName($fileConfig->defaultImage());
        }

        return $fileAssistant;
    }
    /**************** non-static functions END ********************/
}
