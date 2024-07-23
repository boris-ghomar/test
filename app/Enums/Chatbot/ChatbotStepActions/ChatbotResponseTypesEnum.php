<?php

namespace App\Enums\Chatbot\ChatbotStepActions;

use App\Enums\Chatbot\Messenger\ChatbotMessageTypesEnum;
use App\Enums\Resources\ImageConfigEnum;
use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\FileAssistant;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;

enum ChatbotResponseTypesEnum implements Translatable
{
    /**
     * Notice:
     * When you want to update these cases, you must update
     * "setDynamicDataToEditBotResponse" function in "chatbotCreator.js"
     */
    use EnumActions;

    case Text;
    case RandomText;
    case Image;
    case Button;

    const
        KEY_TEXT_VALUE = "TextValue",
        KEY_TEXTS = "Texts",
        KEY_FILE_NAME = "FileName",
        KEY_TITLE = "Title",
        KEY_TYPE = "Type",
        KEY_TARGET_STEP = "TargetStep",
        KEY_TARGET_URL = "TargetUrl",
        KEY_GO_TO_STEP = "GoToStep",
        KEY_OPEN_URL = "OpenUrl";

    const
        KEY_MESSAGE = "Message",
        KEY_DATA = "Data";



    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Enum.ChatbotResponseTypes.' . $this->name);
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
            self::Text => [self::KEY_TEXT_VALUE => ""],

            self::RandomText => [
                self::KEY_TEXTS => [],
            ],

            self::Image => [self::KEY_FILE_NAME => null],

            self::Button => [
                self::KEY_TITLE => null,
                self::KEY_TYPE => null, // GoToStep|OpenUrl
                self::KEY_TARGET_STEP => null,
                self::KEY_TARGET_URL => null,
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

            case self::RandomText:
                if ($key == self::KEY_TEXTS)
                    return array_values($value);
                break;
            case self::Button:
                // Remove unsed data
                if ($key == self::KEY_TARGET_STEP && $data[self::KEY_TYPE] != self::KEY_GO_TO_STEP)
                    return null;
                else if ($key == self::KEY_TARGET_URL && $data[self::KEY_TYPE] != self::KEY_OPEN_URL)
                    return null;
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

        // Text
        if ($this == self::Text) {

            if (isset($data[self::KEY_TEXT_VALUE])) {

                $message = $data[self::KEY_TEXT_VALUE];

                if (!empty($message)) {
                    return [
                        self::KEY_TYPE => ChatbotMessageTypesEnum::Text->name,
                        self::KEY_DATA => [
                            self::KEY_MESSAGE => $message
                        ]
                    ];
                }
            }
        }
        // RandomText
        else if ($this == self::RandomText) {

            if (isset($data[self::KEY_TEXTS])) {

                $texts = $data[self::KEY_TEXTS];

                if (is_null($texts)) {
                    $message = null;
                } else {

                    $message = count($texts) > 0 ? Arr::random($texts) : null;
                }

                if (!empty($message)) {
                    return [
                        self::KEY_TYPE => ChatbotMessageTypesEnum::Text->name,
                        self::KEY_DATA => [
                            self::KEY_MESSAGE => $message
                        ]
                    ];
                }
            }
        }
        // Image
        else if ($this == self::Image) {

            if (isset($data[self::KEY_FILE_NAME])) {

                $fileName = $data[self::KEY_FILE_NAME];
                if (!empty($fileName)) {

                    $file = new FileAssistant(ImageConfigEnum::ChatbotImageResponse, $fileName);
                    if ($file->isFileExists()) {
                        return [
                            self::KEY_TYPE => ChatbotMessageTypesEnum::Image->name,
                            self::KEY_DATA => [
                                self::KEY_FILE_NAME => $fileName
                            ]
                        ];
                    }
                }
            }
        }
        // Button
        else if ($this == self::Button) {

            if (empty($data[self::KEY_TITLE]) || empty($data[self::KEY_TYPE])) return null;
            if (empty($data[self::KEY_TARGET_STEP]) && empty($data[self::KEY_TARGET_URL])) return null;

            // Encrypt target step id for security
            if ($data[self::KEY_TYPE] == self::KEY_GO_TO_STEP) {
                $data[self::KEY_TARGET_STEP] = Crypt::encrypt($data[self::KEY_TARGET_STEP]);
            }

            return [
                self::KEY_TYPE => ChatbotMessageTypesEnum::Button->name,
                self::KEY_DATA => $data
            ];
        }

        return null;
    }
}
