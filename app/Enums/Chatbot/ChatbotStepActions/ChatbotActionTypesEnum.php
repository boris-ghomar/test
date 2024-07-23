<?php

namespace App\Enums\Chatbot\ChatbotStepActions;

use App\Enums\Tickets\TicketPrioritiesEnum;
use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum ChatbotActionTypesEnum implements Translatable
{
    /**
     * Notice:
     * When you want to update these cases, you must update
     * "setDynamicDataToEditBotResponse" function in "chatbotCreator.js"
     */
    use EnumActions;

    case GoToStep;
    case StartTicket;
    case MakeTicket;
        // case OpenLiveChat;
    case End;

    const
        KEY_IS_FINAL_STEP = "IsFinalStep",

        // GoToStep
        KEY_TARGET_STEP = "TargetStep",

        // StartTicket
        KEY_TICKET_SUBJECT = "TicketSubject",
        KEY_TICKET_HOUR_LIMIT = "HourLimit",
        KEY_TICKET_NUMBER_LIMIT = "NumberLimit",
        KEY_TICKET_SCHEDULE_FAILED_TARGET_STEP = "ScheduleFaildTargetStep",

        // MakeTicket
        KEY_TICKET_PRIORITY = "TicketPriority";

    const
        KEY_TYPE = "Type",
        KEY_DATA = "Data";

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Enum.ChatbotActionTypes.' . $this->name);
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

            self::GoToStep => [
                self::KEY_IS_FINAL_STEP => true,
                self::KEY_TARGET_STEP => null,
            ],

            self::StartTicket => [
                self::KEY_IS_FINAL_STEP => false,
                self::KEY_TICKET_SUBJECT => null,
                self::KEY_TICKET_HOUR_LIMIT => null,
                self::KEY_TICKET_NUMBER_LIMIT => null,
                self::KEY_TICKET_SCHEDULE_FAILED_TARGET_STEP => null,
            ],
            self::MakeTicket => [
                self::KEY_IS_FINAL_STEP => false,
                self::KEY_TICKET_PRIORITY => TicketPrioritiesEnum::Normal->name,
            ],
            self::End => [
                self::KEY_IS_FINAL_STEP => true,
            ],

            default => []
        };


        if (!is_null($data)) {
            // Repllace existing data with model default data

            $notAllowedToChange = [self::KEY_IS_FINAL_STEP];

            foreach ($model as $key => $value) {

                if (!in_array($key, $notAllowedToChange) && isset($data[$key]))
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

            case self::End:
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
     * @return ?array
     */
    public function makeChatMessageContent(?array $data): ?array
    {
        if (is_null($data)) return null;

        // StartTicket
        if ($this == self::StartTicket) {

            return [
                self::KEY_TYPE => $this->name,
                self::KEY_DATA => [
                    self::KEY_TICKET_SUBJECT => isset($data[self::KEY_TICKET_SUBJECT]) ? $data[self::KEY_TICKET_SUBJECT] : null,
                ]
            ];
        }
        // MakeTicket
        else if ($this == self::MakeTicket) {

            return [
                self::KEY_TYPE => $this->name,
                self::KEY_DATA => []
            ];
        }

        return null;
    }
}
