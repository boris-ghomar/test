<?php

namespace App\Models\BackOffice\Chatbot;

use App\Enums\Chatbot\ChatbotStepActions\ChatbotUserInputTypesEnum;
use App\Enums\Database\Tables\ChatbotChatsTableEnum;
use App\Enums\Database\Tables\ChatbotMessagesTableEnum as TableEnum;
use App\Enums\Database\Tables\ChatbotStepsTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\HHH_Library\general\php\JsonHelper;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ChatbotMessage extends SuperModel
{
    use HasFactory;

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
            TableEnum::ChatbotChatId->dbName(),
            TableEnum::ChatbotStepId->dbName(),
            TableEnum::IsBotMessage->dbName(),
            TableEnum::Type->dbName(),
            TableEnum::Content->dbName(),
            TableEnum::IsPassed->dbName(),
        ];
        $this->casts = [
            TableEnum::IsBotMessage->dbName() => 'boolean',
            TableEnum::IsPassed->dbName() => 'boolean',
        ];

        parent::__construct($attributes);
    }

    /**
     * @override parent boot
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {

            $model[TableEnum::Id->dbName()] = Str::orderedUuid()->toString();
            return $model;
        });

        self::saved(function (self $model) {

            $model->chatbotChat->touch();
        });
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/
    /**
     * Get the chatbotChat that owns the ChatbotMessage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chatbotChat(): BelongsTo
    {
        return $this->belongsTo(ChatbotChat::class, TableEnum::ChatbotChatId->dbName(), ChatbotChatsTableEnum::Id->dbName());
    }

    /**
     * Get the chatbotStep that owns the ChatbotMessage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chatbotStep(): BelongsTo
    {
        return $this->belongsTo(ChatbotStep::class, TableEnum::ChatbotStepId->dbName(), ChatbotStepsTableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/
    /**
     * Get profile photo file config
     *
     * @return \App\Enums\Resources\ImageConfigEnum
     */
    public static function getPhotoFileConfig(): ImageConfigEnum
    {
        return ImageConfigEnum::ChatbotUserInputImage;
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

        $fileName = null;
        $content = $this[TableEnum::Content->dbName()];
        $type = $content[ChatbotUserInputTypesEnum::KEY_TYPE];
        if ($type == ChatbotUserInputTypesEnum::Image->name) {
            $fileName = $content[ChatbotUserInputTypesEnum::KEY_USER_ANSWER];
        }

        $fileAssistant = new FileAssistant($fileConfig, $this[$fileName]);

        if ($useFallbackPhoto && !$fileAssistant->isFileExists()) {

            $fileAssistant->setPath($fileConfig->defaultPath());
            $fileAssistant->setName($fileConfig->defaultImage());
        }

        return $fileAssistant;
    }
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Convert string boolean to boolean when saving information.
     * When receiving information from the API client,
     * boolean values may be received in the form of strings.
     *
     * Example:
     *   "true" => true
     *   "false" => false
     *
     * @param mixed $value
     * @return void
     */
    public function setIsBotMessageAttribute(mixed $value): void
    {
        $this->attributes[TableEnum::IsBotMessage->dbName()] = CastEnum::Boolean->cast($value);
    }

    /**
     * Convert string boolean to boolean when saving information.
     * When receiving information from the API client,
     * boolean values may be received in the form of strings.
     *
     * Example:
     *   "true" => true
     *   "false" => false
     *
     * @param mixed $value
     * @return void
     */
    public function setIsPassedAttribute(mixed $value): void
    {
        $this->attributes[TableEnum::IsPassed->dbName()] = CastEnum::Boolean->cast($value);
    }

    /**
     * Set content attribute
     *
     * @param  mixed $value
     * @return void
     */
    public function setContentAttribute(mixed $value): void
    {
        if (!is_null($value) && is_array($value)) {

            $this->attributes[TableEnum::Content->dbName()] = json_encode($value);
        }
    }

    /**
     * Get content attribute
     *
     * @param  mixed $value
     * @return array
     */
    public function getContentAttribute(mixed $value): array
    {
        return JsonHelper::isJson($value) ? json_decode($value, true) : null;
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
    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
