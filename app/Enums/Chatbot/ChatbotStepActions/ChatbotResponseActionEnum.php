<?php

namespace App\Enums\Chatbot\ChatbotStepActions;

use App\Enums\Database\Tables\ChatbotChatsTableEnum;
use App\Enums\Database\Tables\ChatbotMessagesTableEnum;
use App\Enums\Database\Tables\ChatbotStepsTableEnum;
use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Models\BackOffice\Chatbot\ChatbotChat;
use App\Models\BackOffice\Chatbot\ChatbotMessage;
use App\Models\BackOffice\Chatbot\ChatbotStep;

enum ChatbotResponseActionEnum implements Translatable
{
    use EnumActions;

    case Delay;
    case Type;
    case Data;


    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return match ($this) {
            self::Delay => __('thisApp.AdminPages.Chatbot.Form.StepDelay'),
            self::Type => __('thisApp.AdminPages.Chatbot.Form.StepType'),

            default => $this->name
        };
    }

    /**
     * Get model of action
     *
     * @param  array|null $data
     * Data stored in the database in the "action" column.
     * @return array
     */
    public static function getModel(array|null $data = null): array
    {
        $delayKey = self::Delay->name;
        $typeKey = self::Type->name;
        $dataKey = self::Data->name;

        // Default model structure
        $defaultType = ChatbotResponseTypesEnum::Text;

        $model = [
            $delayKey   => 0,
            $typeKey    => $defaultType->name,
            $dataKey    => $defaultType->getModel(null),
        ];

        if (!is_null($data)) {
            // Repllace existing data with model default data

            foreach ($model as $key => $value) {

                if (isset($data[$key]))
                    $model[$key] = $data[$key];
            }

            /** @var ChatbotResponseTypesEnum $typeCase */
            $typeCase = isset($data[$typeKey]) ? ChatbotResponseTypesEnum::getCase($data[$typeKey]) : false;

            if ($typeCase) {

                $model[$dataKey] = $typeCase->getModel($model[$dataKey]);
            }
        }

        return $model;
    }

    /**
     * Make chat message from action data
     *
     * @param  App\Models\BackOffice\Chatbot\ChatbotStep|null $chatbotStep
     * @param \App\Models\BackOffice\Chatbot\ChatbotChat|null $chatbotChat
     * @return \App\Models\BackOffice\Chatbot\ChatbotMessage|null
     */
    public static function makeChatMessage(?ChatbotStep $chatbotStep, ?ChatbotChat $chatbotChat): ?ChatbotMessage
    {
        $delayKey = self::Delay->name;
        $typeKey = self::Type->name;
        $dataKey = self::Data->name;

        if (is_null($chatbotStep) || is_null($chatbotChat)) return null;

        $data = $chatbotStep[ChatbotStepsTableEnum::Action->dbName()];

        if (is_null($data)) return null;

        try {
            /** @var ChatbotResponseTypesEnum $typeCase */
            $typeCase = isset($data[$typeKey]) ? ChatbotResponseTypesEnum::getCase($data[$typeKey]) : false;

            $messageContent = $typeCase ? $typeCase->makeChatMessageContent($data[$dataKey]) : null;

            if (!is_null($messageContent)) {

                $chatbotMessage = new ChatbotMessage([
                    ChatbotMessagesTableEnum::ChatbotChatId->dbName()   => $chatbotChat[ChatbotChatsTableEnum::Id->dbName()],
                    ChatbotMessagesTableEnum::ChatbotStepId->dbName()   => $chatbotStep[ChatbotStepsTableEnum::Id->dbName()],
                    ChatbotMessagesTableEnum::IsBotMessage->dbName()    => true,
                    ChatbotMessagesTableEnum::Type->dbName()            => $messageContent[ChatbotResponseTypesEnum::KEY_TYPE],
                    ChatbotMessagesTableEnum::Content->dbName()         => $messageContent[ChatbotResponseTypesEnum::KEY_DATA],
                    ChatbotMessagesTableEnum::IsPassed->dbName()        => true,
                ]);
                $chatbotMessage->save();

                return $chatbotMessage;
            }
        } catch (\Throwable $th) {

            $errorMessage = sprintf(
                "ChatbotStep: %s\nChatbotChat: %s\nError: %s",
                json_encode($chatbotStep),
                json_encode($chatbotChat),
                $th->getMessage()
            );

            LogCreator::createLogError(get_class(), __FUNCTION__, $errorMessage);
        }

        return null;
    }
}
