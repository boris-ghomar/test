<?php

namespace App\Enums\Chatbot\ChatbotStepActions;

use App\Enums\Chatbot\Messenger\ChatbotMessageTypesEnum;
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
use App\Models\BackOffice\Tickets\Ticket;

enum ChatbotUserInputActionEnum implements Translatable
{
    use EnumActions;

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
            self::Type => __('thisApp.AdminPages.Chatbot.Form.UserInput.StepType'),

            default => $this->name
        };
    }

    /**
     * Get model of action
     *
     * @param  array|null $data
     * @return array
     */
    public static function getModel(array|null $data = null): array
    {
        $typeKey = self::Type->name;
        $dataKey = self::Data->name;

        // Default model structure
        $defaultType = ChatbotUserInputTypesEnum::OneLineText;

        $model = [
            $typeKey    => $defaultType->name,
            $dataKey    => $defaultType->getModel(null),
        ];

        if (!is_null($data)) {
            // Repllace existing data with model default data

            foreach ($model as $key => $value) {

                if (isset($data[$key]))
                    $model[$key] = $data[$key];
            }

            /** @var ChatbotUserInputTypesEnum $typeCase */
            $typeCase = isset($data[$typeKey]) ? ChatbotUserInputTypesEnum::getCase($data[$typeKey]) : false;

            if ($typeCase) {

                $model[$dataKey] = $typeCase->getModel($model[$dataKey]);
            }
        }

        return $model;
    }

    /**
     * Make chat message from action data
     *
     * @param  \App\Models\BackOffice\Chatbot\ChatbotStep|null $chatbotStep
     * @param  \App\Models\BackOffice\Chatbot\ChatbotChat|null $chatbotChat
     * @return \App\Models\BackOffice\Chatbot\ChatbotMessage|null
     */
    public static function makeChatMessage(?ChatbotStep $chatbotStep, ?ChatbotChat $chatbotChat): ?ChatbotMessage
    {
        $typeKey = self::Type->name;
        $dataKey = self::Data->name;

        if (is_null($chatbotStep) || is_null($chatbotChat)) return null;

        $data = $chatbotStep[ChatbotStepsTableEnum::Action->dbName()];

        if (is_null($data)) return null;

        try {
            /** @var ChatbotUserInputTypesEnum $typeCase */
            $typeCase = isset($data[$typeKey]) ? ChatbotUserInputTypesEnum::getCase($data[$typeKey]) : false;

            $messageContent = $typeCase ? $typeCase->makeChatMessageContent($data[$dataKey], $chatbotStep) : null;

            if (!is_null($messageContent)) {

                $chatbotMessage = new ChatbotMessage([
                    ChatbotMessagesTableEnum::ChatbotChatId->dbName()   => $chatbotChat[ChatbotChatsTableEnum::Id->dbName()],
                    ChatbotMessagesTableEnum::ChatbotStepId->dbName()   => $chatbotStep[ChatbotStepsTableEnum::Id->dbName()],
                    ChatbotMessagesTableEnum::IsBotMessage->dbName()    => false,
                    ChatbotMessagesTableEnum::Type->dbName()            => ChatbotMessageTypesEnum::Input->name,
                    ChatbotMessagesTableEnum::Content->dbName()         => $messageContent,
                    ChatbotMessagesTableEnum::IsPassed->dbName()        => false,
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

    /**
     * Make chat message from action data
     *
     * @param null|\App\Models\BackOffice\Chatbot\ChatbotMessage $chatbotMessage
     * @param null|\App\Models\BackOffice\Tickets\Ticket $ticket
     * @return bool
     */
    public static function makeTicketMessage(?ChatbotMessage $chatbotMessage, ?Ticket $ticket): bool
    {
        $typeKey = self::Type->name;
        $dataKey = self::Data->name;

        if (is_null($chatbotMessage) || is_null($ticket)) return false;

        $content = $chatbotMessage[ChatbotMessagesTableEnum::Content->dbName()];

        if (is_null($content)) return false;

        try {
            /** @var ChatbotUserInputTypesEnum $typeCase */
            $typeCase = isset($content[$typeKey]) ? ChatbotUserInputTypesEnum::getCase($content[$typeKey]) : false;

            return $typeCase ? $typeCase->makeTicketMessageContent($chatbotMessage, $ticket) : false;
        } catch (\Throwable $th) {

            $errorMessage = sprintf(
                "Chatbot Message: %s\nTicket: %s\nError: %s",
                json_encode($chatbotMessage),
                json_encode($ticket),
                $th->getMessage()
            );

            LogCreator::createLogError(get_class(), __FUNCTION__, $errorMessage);
        }

        return false;
    }
}
