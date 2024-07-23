<?php

namespace App\Models\BackOffice\Chatbot;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ChatbotsTableEnum as TableEnum;
use App\Enums\Database\Tables\ChatbotStepsTableEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chatbot extends SuperModel
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
            TableEnum::IsActive->dbName(),
            TableEnum::Descr->dbName(),
        ];

        $this->attributes = [
            TableEnum::IsActive->dbName() => 0,
        ];

        $this->casts = [
            TableEnum::IsActive->dbName() => 'boolean',
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

        self::saved(function (self $model) {


            $isActiveCol = TableEnum::IsActive->dbName();

            if ($model[$isActiveCol]) {
                // Deactive other active chatbots

                $idCol = TableEnum::Id->dbName();

                $activeChatbots =  self::where($idCol, '!=', $model->$idCol)
                    ->where($isActiveCol, 1)
                    ->get();

                foreach ($activeChatbots as $chatbot) {

                    if ($chatbot->$isActiveCol) {

                        $chatbot->$isActiveCol = 0;
                        $chatbot->save();
                    }
                }
            }
        });
    }
    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get all of the ChatbotSteps for the ChatBot
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatbotSteps(): HasMany
    {
        return $this->hasMany(ChatbotStep::class, ChatbotStepsTableEnum::ChatbotId->dbName(), TableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/

    /**
     * Check if is there any active chatbot
     *
     * @return bool
     */
    public static function isActiveChatbotAvailable(): bool
    {

        return self::where(TableEnum::IsActive->dbName(), 1)
            ->orderBy(TableEnum::Id->dbName(), 'asc')
            ->exists();
    }

    /**
     * Get chatbot steps tree
     *
     * @param bool $getAsJson
     * @param  int $parentId
     * @return \Illuminate\Database\Eloquent\Collection|string
     */
    public function getStepsTree(bool $getAsJson = true, int $parentId = 0): string|Collection
    {
        $idCol = TableEnum::Id->dbName();
        $translatedStepTypeCol = ChatbotStepsTableEnum::TranslatedStepType->dbName();

        $excludedColumns = [
            TimestampsEnum::CreatedAt->dbName(),
            TimestampsEnum::UpdatedAt->dbName(),
        ];

        $steps = $this->chatbotSteps()
            ->Exclude($excludedColumns)
            ->where(ChatbotStepsTableEnum::ParentId->dbName(), $parentId)
            ->orderBy(ChatbotStepsTableEnum::Position->dbName(), 'asc')
            ->get();

        foreach ($steps as $step) {
            $step[$translatedStepTypeCol] = $step->$translatedStepTypeCol;
            $step['childs'] = $this->getStepsTree(false, $step->$idCol);
        }

        return $getAsJson ? json_encode($steps) : $steps;
    }

    /**
     * Get profile photo file config
     *
     * @return \App\Enums\Resources\ImageConfigEnum
     */
    public static function getPhotoFileConfig(): ImageConfigEnum
    {
        return ImageConfigEnum::ChatbotProfileImage;
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

        $fileAssistant = new FileAssistant($fileConfig, $this[TableEnum::ProfilePhotoName->dbName()]);

        if ($useFallbackPhoto && !$fileAssistant->isFileExists()) {

            $fileAssistant->setName(AppSettingsEnum::ChatbotProfileImage->getValue());

            if ($useFallbackPhoto && !$fileAssistant->isFileExists()) {

                $fileAssistant->setPath($fileConfig->defaultPath());
                $fileAssistant->setName($fileConfig->defaultImage());
            }
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
    public function setIsActiveAttribute(mixed $value): void
    {
        $this->attributes[TableEnum::IsActive->dbName()] = CastEnum::Boolean->cast($value);
    }

    /**
     * Get the edit post URL
     *
     * @return string
     */
    public function getEditUrlAttribute(): string
    {
        return AdminPublicRoutesEnum::Chatbots_EditBot->url(['chatbot' => $this[TableEnum::Id->dbName()]]);
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

    /**
     * Get scope of active chatbots
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(TableEnum::IsActive->dbName(), 1);
    }

    /**
     * Get scope of active chatbots
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeNotActive(Builder $query): Builder
    {
        return $query->where(TableEnum::IsActive->dbName(), 0);
    }

    /**
     * Scope a collection of scopes for get all items.
     *
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllItems(Builder $query, array $filter): Builder
    {
        return $query
            ->IsActive($filter)
            ->Description($filter);
    }

    /**
     * Scope a collection of scopes for the "Controller->apiIndex" function.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApiIndexCollection(Builder $query, array $filter): Builder
    {
        return $query
            ->AllItems($filter)
            ->SortOrder($filter);
    }
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/

    /**
     * Scope a query to set SortOrder as request or defults.
     *
     * @param array $replaceSortFields
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @param  array $replaceSortFields
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOrder(Builder $query, array $filter = null, array $replaceSortFields = []): Builder
    {
        return $this->superScopeSortOrder($query, $filter, function ($query) {
            return $query
                ->orderBy(TableEnum::IsActive->dbName(), 'desc')
                ->orderBy(TableEnum::Name->dbName(), 'asc');
        }, $replaceSortFields);
    }


    /**
     * Scope a query to only include "is_active" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeCheckbox(TableEnum::IsActive->dbName(), $query, $filter);
    }

    /**
     * Scope a query to only include "descr" as request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param ?array $filter input data array
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDescription(Builder $query, array $filter = null): Builder
    {
        return $this->superScopeLikeAs(TableEnum::Descr->dbName(), $query, $filter);
    }
    /**************** scopes END ********************/
}
