<?php

namespace App\Enums\Chatbot\ChatbotStepActions;

use App\Enums\Database\Tables\ChatbotMessagesTableEnum;
use App\Enums\Database\Tables\ChatbotStepsTableEnum;
use App\Enums\Database\Tables\TicketMessagesTableEnum;
use App\Enums\Tickets\TicketMessageTypesEnum;
use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Models\BackOffice\Chatbot\ChatbotMessage;
use App\Models\BackOffice\Chatbot\ChatbotStep;
use App\Models\BackOffice\Tickets\Ticket;
use App\Models\BackOffice\Tickets\TicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

enum ChatbotUserInputTypesEnum implements Translatable
{
    /**
     * Notice:
     * When you want to update these cases, you must update
     * "setDynamicDataToEditBotResponse" function in "chatbotCreator.js"
     */
    use EnumActions;

    case Number;
    case OneLineText;
    case MultipleLineText;
    case Image;

    const
        KEY_TITLE = "Title",
        KEY_DESCRIPTION = "Description",
        KEY_PALCEHOLDER = "Placeholder",
        KEY_REQUIRED = "Required",
        KEY_MIN = "Min",
        KEY_MAX = "Max",
        KEY_MIN_LENGTH = "MinLenght",
        KEY_MAX_LENGTH = "MaxLenght";

    const
        KEY_STEP_ID = "StepId",
        KEY_TYPE = "Type",
        KEY_DATA = "Data",
        KEY_USER_ANSWER = "UserAnswer";

    /**
     * Get key full name base on case
     *
     * @param  string $key KEY_TITLE | KEY_DESCRIPTION | ...
     * @return string
     */
    private function getKeyName(string $key): string
    {
        $suffix = match ($this) {

            self::Number => "_Num",
            self::OneLineText => "_OLT",
            self::MultipleLineText => "_MLT",
            self::Image => "_Img",

            default => ""
        };

        return $key . $suffix;
    }

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Enum.ChatbotUserInputTypes.' . $this->name);
    }

    /**
     * Get model of response
     *
     * @param  array|null $data
     * @return array
     */
    public function getModel(array|null $data = null): array
    {

        $model = match ($this) {
            self::Number => [
                $this->getKeyName(self::KEY_TITLE) => null,
                $this->getKeyName(self::KEY_DESCRIPTION) => null,
                $this->getKeyName(self::KEY_PALCEHOLDER) => null,
                $this->getKeyName(self::KEY_REQUIRED) => false,
                $this->getKeyName(self::KEY_MIN) => null,
                $this->getKeyName(self::KEY_MAX) => null,
            ],

            self::OneLineText => [
                $this->getKeyName(self::KEY_TITLE) => null,
                $this->getKeyName(self::KEY_DESCRIPTION) => null,
                $this->getKeyName(self::KEY_PALCEHOLDER) => null,
                $this->getKeyName(self::KEY_REQUIRED) => false,
                $this->getKeyName(self::KEY_MIN_LENGTH) => null,
                $this->getKeyName(self::KEY_MAX_LENGTH) => null,
            ],

            self::MultipleLineText => [
                $this->getKeyName(self::KEY_TITLE) => null,
                $this->getKeyName(self::KEY_DESCRIPTION) => null,
                $this->getKeyName(self::KEY_PALCEHOLDER) => null,
                $this->getKeyName(self::KEY_REQUIRED) => false,
                $this->getKeyName(self::KEY_MIN_LENGTH) => null,
                $this->getKeyName(self::KEY_MAX_LENGTH) => null,
            ],

            self::Image => [
                $this->getKeyName(self::KEY_TITLE) => null,
                $this->getKeyName(self::KEY_DESCRIPTION) => null,
                $this->getKeyName(self::KEY_REQUIRED) => false,
            ],

            default => []
        };


        if (!is_null($data)) {
            // Repllace existing data with model default data

            foreach ($model as $key => $value) {

                if (isset($data[$key]))
                    $model[$key] = $this->prepareInput($data, $key, $data[$key]);
            }
        }

        return $model;
    }

    /**
     * Edit value before use
     *
     * @param  string $key
     * @param  mixed $value
     * @return mixed
     */
    private function prepareInput(array|null $data, string $key, mixed $value): mixed
    {
        if (empty($key)) return $value;

        switch ($this) {

            case self::Number:
                //
                break;
        }

        return $value;
    }

    /**
     * Make chat message content from action data
     *
     * @param  ?array $data
     * Data stored in the database in the "action" column in attribute "Data" .
     * @param null|\App\Models\BackOffice\Chatbot\ChatbotStep $chatbotStep
     * @return ?array
     */
    public function makeChatMessageContent(?array $data, ?ChatbotStep $chatbotStep): ?array
    {

        if (is_null($data)) return null;
        if (is_null($chatbotStep)) return null;

        $stepId = Crypt::encrypt($chatbotStep[ChatbotStepsTableEnum::Id->dbName()]);

        // Number
        if ($this == self::Number) {

            return [
                self::KEY_TYPE  => $this->name,
                self::KEY_DATA  => [
                    self::KEY_STEP_ID       => $stepId,
                    self::KEY_TITLE         => $data[$this->getKeyName(self::KEY_TITLE)],
                    self::KEY_DESCRIPTION   => $data[$this->getKeyName(self::KEY_DESCRIPTION)],
                    self::KEY_PALCEHOLDER   => $data[$this->getKeyName(self::KEY_PALCEHOLDER)],
                    self::KEY_REQUIRED      => $data[$this->getKeyName(self::KEY_REQUIRED)],
                    self::KEY_MIN           => $data[$this->getKeyName(self::KEY_MIN)],
                    self::KEY_MAX           => $data[$this->getKeyName(self::KEY_MAX)],
                ],
                self::KEY_USER_ANSWER   => null,
            ];
        }
        // OneLineText | MultipleLineText
        else if ($this == self::OneLineText || $this == self::MultipleLineText) {

            return [
                self::KEY_TYPE  => $this->name,
                self::KEY_DATA  => [
                    self::KEY_STEP_ID       => $stepId,
                    self::KEY_TITLE         => $data[$this->getKeyName(self::KEY_TITLE)],
                    self::KEY_DESCRIPTION   => $data[$this->getKeyName(self::KEY_DESCRIPTION)],
                    self::KEY_PALCEHOLDER   => $data[$this->getKeyName(self::KEY_PALCEHOLDER)],
                    self::KEY_REQUIRED      => $data[$this->getKeyName(self::KEY_REQUIRED)],
                    self::KEY_MIN_LENGTH    => $data[$this->getKeyName(self::KEY_MIN_LENGTH)],
                    self::KEY_MAX_LENGTH    => $data[$this->getKeyName(self::KEY_MAX_LENGTH)],
                ],
                self::KEY_USER_ANSWER   => null,
            ];
        }
        // Image
        else if ($this == self::Image) {

            return [
                self::KEY_TYPE  => $this->name,
                self::KEY_DATA  => [
                    self::KEY_STEP_ID       => $stepId,
                    self::KEY_TITLE         => $data[$this->getKeyName(self::KEY_TITLE)],
                    self::KEY_DESCRIPTION   => $data[$this->getKeyName(self::KEY_DESCRIPTION)],
                    self::KEY_REQUIRED      => $data[$this->getKeyName(self::KEY_REQUIRED)],
                ],
                self::KEY_USER_ANSWER   => null,
            ];
        }

        return null;
    }

    /**
     * Make chat message content from action data
     *
     * @param  ?array $data
     * Data stored in the database in the "chatbot_messages->content" column in attribute "Data" .
     * @param null|\App\Models\BackOffice\Tickets\Ticket $ticket
     * @return bool
     */
    public function makeTicketMessageContent(?ChatbotMessage $chatbotMessage, ?Ticket $ticket): bool
    {
        if (!auth()->check()) return false;
        if (is_null($chatbotMessage) || is_null($ticket)) return false;

        $chatbotMessageContent = $chatbotMessage[ChatbotMessagesTableEnum::Content->dbName()];
        $data = $chatbotMessageContent[self::KEY_DATA];

        if (is_null($data)) return false;

        $userId = User::authUser()->id;

        try {
            // Number | OneLineText | MultipleLineText
            if ($this == self::Number || $this == self::OneLineText || $this == self::MultipleLineText) {

                $ticketMessageContent = sprintf(
                    "%s:\n%s",
                    $data[self::KEY_TITLE],
                    $chatbotMessageContent[self::KEY_USER_ANSWER],
                );

                $ticketMessage = new TicketMessage([
                    TicketMessagesTableEnum::UserId->dbName()   => $userId,
                    TicketMessagesTableEnum::TicketId->dbName() => $ticket->id,
                    TicketMessagesTableEnum::Type->dbName()     => TicketMessageTypesEnum::Text->name,
                    TicketMessagesTableEnum::Content->dbName()  => $ticketMessageContent,
                ]);
                return $ticketMessage->save();
            }
            // Image
            else if ($this == self::Image) {

                $userAnswer = $chatbotMessageContent[self::KEY_USER_ANSWER];

                if (empty($userAnswer)) {
                    // Image input is optional and user did not sent image
                    $ticketMessageContent = sprintf(
                        "%s:\n%s",
                        $data[self::KEY_TITLE],
                        __('thisApp.AdminPages.Tickets.TicketMessenger.ClientNotSentImage'),
                    );
                    $imageSave = true;
                }else{
                    $ticketMessageContent = $data[self::KEY_TITLE];
                }

                // Create text message for image title
                $ticketMessage = new TicketMessage([
                    TicketMessagesTableEnum::UserId->dbName()   => $userId,
                    TicketMessagesTableEnum::TicketId->dbName() => $ticket->id,
                    TicketMessagesTableEnum::Type->dbName()     => TicketMessageTypesEnum::Text->name,
                    TicketMessagesTableEnum::Content->dbName()  => $ticketMessageContent,
                ]);
                $titleSave = $ticketMessage->save();

                // Create image message for user input image
                if (!empty($userAnswer)) {

                    $ticketMessage = new TicketMessage([
                        TicketMessagesTableEnum::UserId->dbName()   => $userId,
                        TicketMessagesTableEnum::TicketId->dbName() => $ticket->id,
                        TicketMessagesTableEnum::Type->dbName()     => TicketMessageTypesEnum::ChatbotImage->name,
                        TicketMessagesTableEnum::Content->dbName()  => $chatbotMessageContent[self::KEY_USER_ANSWER],
                    ]);
                    $imageSave = $ticketMessage->save();
                }

                return $titleSave && $imageSave;
            }
        } catch (\Throwable $th) {

            $errorMessage = sprintf(
                "Ticket: %s\nData: %s\nError: %s",
                json_encode($ticket),
                json_encode($data),
                $th->getMessage()
            );

            LogCreator::createLogError(get_class(), __FUNCTION__, $errorMessage);
        }


        return false;
    }
}
