<?php

namespace App\Models\General;

use App\Enums\Database\Tables\SettingsTableEnum;
use App\Enums\Database\Tables\UserSettingsTableEnum as TableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Models\BackOffice\Settings\Setting;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserSetting extends SuperModel
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
            TableEnum::SettingId->dbName(),
            TableEnum::Value->dbName(),
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

        // Only load records that belong to the auth user
        static::addGlobalScope(ModelGlobalScopesEnum::UserSetting_UserPersonalSetting->name, function (Builder $builder) {

            $userId = auth()->check()  ? auth()->user()->id : null;

            $builder->where(TableEnum::UserId->dbName(), $userId);
        });

        static::creating(function (UserSetting $userSetting) {

            $userSetting[TableEnum::Id->dbName()] = Str::orderedUuid()->toString();
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the user that owns the UserSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, TableEnum::UserId->dbName());
    }

    /**
     * Get the Setting that owns the UserSetting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class, TableEnum::SettingId->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/

    /**
     * Get the user settings for the key,
     * returning the default value if no user settings exist.
     *
     * @param  ?string $default : null => use system default
     * @param  \App\Enums\Settings\AppSettingsEnum $key
     * @return mixed
     */
    public static function get(AppSettingsEnum $key, ?string $default = null): mixed
    {
        $value = null;

        $setting = Setting::getItemFullRecord($key);

        $userSetting = $setting->userSetting;

        if (is_null($userSetting)) {

            $value = is_null($default) ? $setting->value : $default;
        } else
            $value = $userSetting->value;

        return $key->cast($value);
    }

    /**
     * Save item to database
     * create and update and delete base on request
     *
     * @param  \App\Enums\Settings\AppSettingsEnum $key
     * @param  mixed $value
     * @return void
     */
    public static function saveItem(AppSettingsEnum $key, mixed $value): void
    {
        if (auth()->check()) {

            $setting = Setting::getItemFullRecord($key);
            $userSetting = $setting->userSetting;

            if (empty($value)) {
                // User wants to use system default
                if (!is_null($userSetting))
                    $userSetting->delete();
            } else {

                if (is_null($userSetting)) {
                    // New user settings
                    $userSetting = UserSetting::make([
                        TableEnum::UserId->dbName() => auth()->user()->id,
                        TableEnum::SettingId->dbName() => $setting[SettingsTableEnum::Id->dbName()],
                    ]);
                }

                $userSetting->value = $value;
                $userSetting->save();
            }
        }
    }
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
