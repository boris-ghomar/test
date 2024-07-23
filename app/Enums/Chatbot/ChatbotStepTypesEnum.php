<?php

namespace App\Enums\Chatbot;

use App\Enums\Chatbot\ChatbotStepActions\ChatbotActionActionEnum;
use App\Enums\Chatbot\ChatbotStepActions\ChatbotFilterActionEnum;
use App\Enums\Chatbot\ChatbotStepActions\ChatbotResponseActionEnum;
use App\Enums\Chatbot\ChatbotStepActions\ChatbotUserInputActionEnum;
use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum ChatbotStepTypesEnum implements Translatable
{
    use EnumActions;

    case BotResponse;
    case UserInput;
    case Filter;
    case BotAction;


    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Enum.ChatbotSteps.' . $this->name);
    }

    /**
     * Get step action model
     *
     * @param null|array $data
     * @return array
     */
    public function getActionModel(array|null $data = null): array
    {
        return match ($this) {
            self::BotResponse   => ChatbotResponseActionEnum::getModel($data),
            self::UserInput     => ChatbotUserInputActionEnum::getModel($data),
            self::Filter        => ChatbotFilterActionEnum::getModel($data),
            self::BotAction     => ChatbotActionActionEnum::getModel($data),

            default => []
        };
    }
}
