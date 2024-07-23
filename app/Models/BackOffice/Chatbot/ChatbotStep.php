<?php

namespace App\Models\BackOffice\Chatbot;

use App\Enums\Chatbot\ChatbotStepTypesEnum;
use App\Enums\Database\Tables\ChatbotsTableEnum;
use App\Enums\Database\Tables\ChatbotStepsTableEnum as TableEnum;
use App\HHH_Library\general\php\JsonHelper;
use App\HHH_Library\general\php\ModelHelper;
use App\Models\SuperClasses\SuperModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotStep extends SuperModel
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
            TableEnum::ChatbotId->dbName(),
            TableEnum::ParentId->dbName(),
            TableEnum::Type->dbName(),
            TableEnum::Name->dbName(),
            TableEnum::Action->dbName(),
            TableEnum::Position->dbName(),
        ];

        parent::__construct($attributes);
    }

    /**************** Parnet Items END ********************/

    /**************** Relationships ********************/

    /**
     * Get the Chatbot that owns the ChatbotStep
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chatbot(): BelongsTo
    {
        return $this->belongsTo(Chatbot::class, TableEnum::ChatbotId->dbName(), ChatbotsTableEnum::Id->dbName());
    }

    /**
     * Get all of the Childs for the ChatbotStep
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs(): HasMany
    {
        return $this->hasMany(self::class, TableEnum::ParentId->dbName(), TableEnum::Id->dbName());
    }

    /**
     * Get the parentChatbotStep that owns the ChatbotStep
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentChatbotStep(): BelongsTo
    {
        return $this->belongsTo(self::class, TableEnum::ParentId->dbName(), TableEnum::Id->dbName());
    }
    /**************** Relationships END ********************/

    /************************ Exclusive Items ****************************/


    /**
     * Check if the ChatbotStep is a subset of the target ChatbotStep
     *
     * @param  self $targetStep
     * @return bool
     */
    public function isSubsetOf(self $targetStep): bool
    {
        // Check direct subset
        if ($this[TableEnum::ParentId->dbName()] == $targetStep[TableEnum::Id->dbName()])
            return true;

        // Check indirect subset
        if ($parentStep = $this->parentChatbotStep)
            return $parentStep->isSubsetOf($targetStep);

        return false;
    }
    /************************ Exclusive Items END ****************************/

    /**************** Accessors & Mutators ********************/

    /**
     * Get translated name of step type
     *
     * @return string
     */
    public function getTranslatedStepTypeAttribute(): string
    {
        $stepType = $this[TableEnum::Type->dbName()];

        /** @var ChatbotStepTypesEnum  $stepTypeCase*/
        $stepTypeCase = ChatbotStepTypesEnum::getCase($stepType);

        if (!is_null($stepTypeCase)) {

            return $stepTypeCase->translate();
        }

        return $stepType;
    }

    /**
     * Set action attribute
     *
     * @param  mixed $value
     * @return void
     */
    public function setActionAttribute(mixed $value): void
    {
        if (!is_null($value) && is_array($value)) {

            /** @var ChatbotStepTypesEnum $chatbotTypeCase  */
            $chatbotTypeCase = ChatbotStepTypesEnum::getCase($this[TableEnum::Type->dbName()]);

            $value = $chatbotTypeCase->getActionModel($value);

            $this->attributes[TableEnum::Action->dbName()] = json_encode($value);
        }
    }

    /**
     * Get action attribute
     *
     * @param  mixed $value
     * @return array
     */
    public function getActionAttribute(mixed $value): array
    {
        /** @var ChatbotStepTypesEnum $chatbotTypeCase  */
        $chatbotTypeCase = ChatbotStepTypesEnum::getCase($this[TableEnum::Type->dbName()]);

        $value = JsonHelper::isJson($value) ? json_decode($value, true) : null;

        return $chatbotTypeCase->getActionModel($value);
    }

    /**************** Accessors & Mutators END ********************/

    /**************** scopes Collection ********************/

    /**
     * get scope of excluded columns
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $excludedColumns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExclude(Builder $query, array $excludedColumns = []): Builder
    {
        $columns = ModelHelper::getColumnList(self::class);

        return $query->select(array_diff($columns, $excludedColumns));
    }
    /**************** scopes Collection END ********************/

    /**************** scopes ********************/
    /**************** scopes END ********************/
}
