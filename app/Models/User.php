<?php

namespace App\Models;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\BetsTableEnum;
use App\Enums\Database\Tables\ChatbotChatsTableEnum;
use App\Enums\Database\Tables\ChatbotsTableEnum;
use App\Enums\Database\Tables\ChatbotTestersTableEnum;
use App\Enums\Database\Tables\ClientSyncsTableEnum;
use App\Enums\Database\Tables\ClientTrustScoresTableEnum;
use App\Enums\Database\Tables\CustomizedPagesTableEnum;
use App\Enums\Database\Tables\NotificationsTableEnum;
use App\Enums\Database\Tables\PersonnelExtrasTableEnum;
use App\Enums\Database\Tables\ReferralCustomSettingsTableEnum;
use App\Enums\Database\Tables\ReferralsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UserSettingsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\General\ModelGlobalScopesEnum;
use App\Enums\General\PartnerEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Enums\Routes\AdminRoutesEnum;
use App\Enums\Routes\RouteTypesEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Routes\SiteRoutesEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Enums\Users\UsersTypesEnum;
use App\HHH_Library\Calendar\CalendarHelper;
use App\HHH_Library\general\php\Enums\CalendarTypeEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\BackOffice\Settings\Setting;
use App\Models\General\Role;
use App\Models\General\UserSetting;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Models\BackOffice\Bets\Bet;
use App\Models\BackOffice\Chatbot\Chatbot;
use App\Models\BackOffice\Chatbot\ChatbotChat;
use App\Models\BackOffice\Chatbot\ChatbotTester;
use App\Models\BackOffice\ClientsManagement\ClientTrustScore;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use App\Models\BackOffice\Referral\Referral;
use App\Models\BackOffice\Referral\ReferralCustomSetting;
use App\Models\BackOffice\Sync\ClientSync;
use App\Models\General\CustomizedPage;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        // 'profile_photo_url',
    ];

    /**************** Relationships ********************/

    /**
     * Get the Role that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, UsersTableEnum::RoleId->dbName(), RolesTableEnum::Id->dbName());
    }

    /**
     * Get user extra data
     *
     * @return HasOne
     */
    public function userExtra(): ?HasOne
    {
        if ($this->isPersonnel())
            return $this->personnel->personnelExtra();
        else if ($this->isClient())
            return $this->userBetconstruct->betconstructClient();

        return null;
    }

    /**
     * Get user "personnel" model
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function personnel(): ?HasOne
    {
        return $this->isPersonnel() ? $this->hasOne(Personnel::class, UsersTableEnum::Id->dbName())->withTrashed() : null;
    }

    /**
     * Get user "userBetconstruct" model
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function userBetconstruct(): ?HasOne
    {
        return $this->isClient() ? $this->hasOne(UserBetconstruct::class, UsersTableEnum::Id->dbName())->withTrashed() : null;
    }

    /**
     * Get user "betconstructClient" model
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function betconstructClient(): ?HasOne
    {
        return $this->isClient() ? $this->hasOne(BetconstructClient::class, ClientModelEnum::UserId->dbName(), UsersTableEnum::Id->dbName()) : null;
    }

    /**
     * Get all of the UserSettings for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userSettings(): HasMany
    {
        return $this->hasMany(UserSetting::class, UserSettingsTableEnum::UserId->dbName())
            ->withoutGlobalScope(ModelGlobalScopesEnum::UserSetting_UserPersonalSetting->name);
    }

    /**
     * Get all of the chatbotChats for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatbotChats(): HasMany
    {
        return $this->hasMany(ChatbotChat::class, ChatbotChatsTableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get user "clientTrustScore" model
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function clientTrustScore(): ?HasOne
    {
        return $this->isClient() ? $this->hasOne(ClientTrustScore::class, ClientTrustScoresTableEnum::UserId->dbName(), UsersTableEnum::Id->dbName()) : null;
    }

    /**
     * Get user "CustomizedPages" model
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany|null
     */
    public function customizedPages(): ?HasMany
    {
        return $this->hasMany(CustomizedPage::class, CustomizedPagesTableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get user "ClientSync" model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function clientSync(): ?HasOne
    {
        if ($this->isPersonnel())
            return null;

        $relation = $this->hasOne(ClientSync::class, ClientSyncsTableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());

        if (!$relation->exists()) {
            // Create sync record for client

            $clientSync = new ClientSync();
            $clientSync[ClientSyncsTableEnum::UserId->dbName()] = $this->id;

            $clientSync->save();
        }

        return $relation;
    }

    /**
     * Get user "Bet" model
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany|null
     */
    public function clientBets(): ?HasMany
    {
        if ($this->isPersonnel())
            return null;

        return $this->hasMany(Bet::class, BetsTableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get client referral record from referrals table
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function clientReferral(): ?HasOne
    {
        if ($this->isPersonnel())
            return null;

        return $this->hasOne(Referral::class, ReferralsTableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get referred referrals model data of client
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany|null
     */
    public function clientReferrals(): ?HasMany
    {
        if ($this->isPersonnel())
            return null;

        return $this->hasMany(Referral::class, ReferralsTableEnum::ReferredBy->dbName(), UsersTableEnum::Id->dbName());
    }

    /**
     * Get referral custom settings of client
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function clientReferralCustomSettings(): ?HasOne
    {
        if ($this->isPersonnel())
            return null;

        return $this->hasOne(ReferralCustomSetting::class, ReferralCustomSettingsTableEnum::UserId->dbName(), UsersTableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /**************** Exclusive Items ********************/

    /**
     * get auth user
     *
     * @return self|null
     */
    public static function authUser(): self|null
    {
        return auth()->user();
    }

    /**
     * Super Admin User detection
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return
            $this[UsersTableEnum::Id->dbName()] == 1
            && $this[UsersTableEnum::Type->dbName()] === UsersTypesEnum::Personnel->name
            && $this[UsersTableEnum::Email->dbName()] === "ferhadkonar@gmail.com";
    }

    /**
     * Check user account is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $isUserActive = ($this[UsersTableEnum::Status->dbName()] === UsersStatusEnum::Active->name);
        $isUserRoleActive = $this->role[RolesTableEnum::IsActive->dbName()];

        return $isUserActive && $isUserRoleActive;
    }

    /**
     * Personnel User detection
     * @return bool
     */
    public function isPersonnel(): bool
    {
        return $this[UsersTableEnum::Type->dbName()] === UsersTypesEnum::Personnel->name;
    }

    /**
     * Clinet User detection
     * @return bool
     */
    public function isClient(): bool
    {
        return !$this->isPersonnel();
    }

    /**
     * Get the last 'notifications' for the 'user'
     */
    public function lastNotifications($count)
    {
        return $this->notifications()
            ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'desc')
            ->orderBy(NotificationsTableEnum::ReadAt->dbName(), 'asc')
            ->limit($count)
            ->get();
    }

    /**
     * Get profile photo file config
     *
     * @return \App\Enums\Resources\ImageConfigEnum
     */
    public static function getPhotoFileConfig(): ImageConfigEnum
    {
        return ImageConfigEnum::ProfilePhoto;
    }

    /**
     * Get photo file assistant
     *
     * @param bool $useFallbackPhoto : true => if file not exists, it will be return fallback image (no profile)
     * @return \App\HHH_Library\general\php\FileAssistant
     */
    public function getPhotoFileAssistant(bool $useFallbackPhoto = true): FileAssistant
    {
        $fileConfig = $this->getPhotoFileConfig();

        $fileAssistant = new FileAssistant($fileConfig, $this[UsersTableEnum::ProfilePhotoName->dbName()]);

        if ($useFallbackPhoto && !$fileAssistant->isFileExists()) {

            $fileAssistant->setPath($fileConfig->defaultPath());
            $fileAssistant->setName($fileConfig->defaultImage());
        }

        return $fileAssistant;
    }

    /**
     * This function sets and returns the "CalendarHelper" class
     * based on the user and default settings.
     *
     * @return \App\HHH_Library\Calendar\CalendarHelper
     */
    public function getCalendarHelper(): CalendarHelper
    {
        $routeType = RouteTypesEnum::type();

        if ($routeType === RouteTypesEnum::AdminPanel) {

            $timezone = AppSettingsEnum::AdminPanelTimeZone;
            $calendarType = AppSettingsEnum::AdminPanelCalendarType;
            $canChangeTimeZone = Setting::get(AppSettingsEnum::canPersonnelChangeTimeZone);
            $canChangeCalendarType = Setting::get(AppSettingsEnum::canPersonnelChangeCalendarType);
        } else {

            $timezone = AppSettingsEnum::CommunityTimeZone;
            $calendarType = AppSettingsEnum::CommunityCalendarType;
            $canChangeTimeZone = Setting::get(AppSettingsEnum::canClientChangeTimeZone);
            $canChangeCalendarType = Setting::get(AppSettingsEnum::canClientChangeCalendarType);
        }

        return (new CalendarHelper(
            $canChangeTimeZone ? UserSetting::get($timezone) : Setting::get($timezone),
            CalendarTypeEnum::getCase(($canChangeCalendarType ? UserSetting::get($calendarType) : Setting::get($calendarType)))
        ));
    }

    /**
     * Get user calendar type
     *
     * @return \App\HHH_Library\general\php\Enums\CalendarTypeEnum
     */
    public function getCalendarType(): CalendarTypeEnum
    {
        return $this->getCalendarHelper()->getCalendarType();
    }

    /**
     * This function converts UTC input time
     * to local time and user calendar type
     * based on the user and default settings.
     *
     * @param  ?string  $UTC_DateTime [DateTimeString]
     * @param bool $onlyDate
     * @param  ?string $format Exmp: "Y-m-d H:i:s"
     * @return ?string   Local [DateTimeString]
     */
    public function convertUTCToLocalTime(?string $UTC_DateTime, bool $onlyDate = false, ?string $format = null): ?string
    {
        if (is_null($UTC_DateTime))
            return null;

        $calendarHelper = $this->getCalendarHelper();

        if ($onlyDate)
            $calendarHelper->setformat($calendarHelper->getCalendarType()->defaultDateFormat());

        return $calendarHelper
            ->setFormat($format)
            ->convertToLocalDate($UTC_DateTime);
    }

    /**
     * This function takes a local date and time
     * and converts it to UTC date in Gregorian date format
     * based on the user and default settings.
     *
     * @param  ?string $localDateTime [DateTimeString]
     * @param bool $onlyDate
     * @param  ?string $format Exmp: "Y-m-d H:i:s"
     * @return ?string UTC_DateTime [DateTimeString]
     */
    public function convertLocalTimeToUTC(?string $localDateTime, bool $onlyDate = false, ?string $format = null): ?string
    {
        if (is_null($localDateTime))
            return null;

        $calendarHelper = $this->getCalendarHelper();

        if ($onlyDate)
            $calendarHelper->setformat($calendarHelper->getCalendarType()->defaultDateFormat());

        return $calendarHelper
            ->setFormat($format)
            ->convertToUTC($localDateTime);
    }

    /**
     * Get responsive chatbot ID for the user
     *
     * @return int|false
     */
    public function getResponsiveChatbotId(): int|false
    {
        // Check if the client is assigned for a test chatbot
        $chatbotTester = ChatbotTester::where(ChatbotTestersTableEnum::UserId->dbName(), $this[UsersTableEnum::Id->dbName()])
            ->first();

        if (!is_null($chatbotTester)) {

            $chatbotId = $chatbotTester[ChatbotTestersTableEnum::ChatbotId->dbName()];
            if (Chatbot::find($chatbotId))
                return $chatbotId;
        }

        // return active chatbot ID
        $activeChatbot = Chatbot::where(ChatbotsTableEnum::IsActive->dbName(), 1)
            ->orderBy(ChatbotsTableEnum::Id->dbName(), 'asc')
            ->first();

        if (!is_null($activeChatbot))
            return $activeChatbot[ChatbotsTableEnum::Id->dbName()];

        return false;
    }

    /**
     * Get user "CustomizedPage" record for user
     *
     * @param \App\Enums\Routes\AdminRoutesEnum|\App\Enums\Routes\AdminPublicRoutesEnum|\App\Enums\Routes\SiteRoutesEnum|\App\Enums\Routes\SitePublicRoutesEnum $route
     * @return \App\Models\General\CustomizedPage|null
     */
    public function customizedPage(AdminRoutesEnum|AdminPublicRoutesEnum|SiteRoutesEnum|SitePublicRoutesEnum $route): ?CustomizedPage
    {
        return $this->customizedPages()->where(CustomizedPagesTableEnum::Route->dbName(), $route->value)->first();
    }

    /**
     * Get the partner of user
     *
     * @return null|\App\Enums\General\PartnerEnum
     */
    public function getPartner(): ?PartnerEnum
    {
        return UsersTypesEnum::getPartnerByCaseName($this[UsersTableEnum::Type->dbName()]);
    }
    /**************** Exclusive Items END ********************/

    /**************** Accessors & Mutators ********************/
    /**
     * This function returns Photo full name.
     * Returns the default Photo file(noPhoto) if it does not exist
     *
     * sample output:
     * "BackOffice/assets_hhh/images/office_profile_photos/no_profile.png"
     *
     * @return ?string
     */
    public function getPhotoFullNameAttribute(): ?string
    {
        return $this->getPhotoFileAssistant()->getFullName();
    }

    /**
     * This function returns Photo url.
     * Returns the default Photo file(noPhoto) if it does not exist
     *
     * sample output:
     * "http://community.cod/assets/upload/images/profile_photos/no_profile.png"
     *
     * @return string
     */
    public function getPhotoUrlAttribute(): string
    {
        return $this->getPhotoFileAssistant()->getUrl();
    }

    /**
     * Get display name of user
     *
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->isPersonnel()) {

            $aliasNameKey = PersonnelExtrasTableEnum::AliasName->dbName();
            $firstNameKey = PersonnelExtrasTableEnum::FirstName->dbName();

            $personnelExtra = $this->personnel->personnelExtra()->select($aliasNameKey, $firstNameKey)->first();

            $displayName =  $personnelExtra[$aliasNameKey];
            return empty($displayName) ? $personnelExtra[$firstNameKey] : $displayName;
        } else {
            $firstNameKey = ClientModelEnum::FirstName->dbName();
            $usernameKey = ClientModelEnum::Login->dbName();

            $clientExtra = $this->betconstructClient()->select($firstNameKey, $usernameKey)->first();

            if (is_null($clientExtra)) {
                return "";
            } else {

                $displayName =  $clientExtra[$firstNameKey];
                return empty($displayName) ? Str::of($clientExtra[$usernameKey])->mask('*', 3) : $displayName;
            }
        }
    }

    /**
     * Interact with the users's isEmailVerified.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function isEmailVerified(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => empty($attributes[UsersTableEnum::EmailVerifiedAt->dbName()]) ? false : true,
        );
    }

    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * Get scope of client users
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClients(Builder $query): Builder
    {
        return $query->where(UsersTableEnum::Type->dbNameWithTable(DatabaseTablesEnum::Users), '!=', UsersTypesEnum::Personnel->name);
    }
    /**************** scopes Collection END ********************/
}
