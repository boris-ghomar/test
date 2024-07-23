<?php

namespace App\Enums\Chatbot\ChatbotStepActions;

use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Interfaces\Translatable;
use App\HHH_Library\general\php\Enums\LocaleEnum;
use App\HHH_Library\general\php\traits\Enums\EnumActions;
use App\Models\BackOffice\Chatbot\ChatbotStep;
use App\Models\User;
use Illuminate\Support\Arr;

enum ChatbotFilterTypesEnum implements Translatable
{
    /**
     * Notice:
     * When you want to update these cases, you must update
     * "setDynamicDataToEditBotResponse" function in "chatbotCreator.js"
     */
    use EnumActions;

    case ClientCategory;

    const
        KEY_ALLOWED_CATEGOLRIES = "AllowedCategories";

    const
        KEY_TYPE = "Type",
        KEY_DATA = "Data",
        KEY_IS_PASSED = "IsPassed";

    /**
     * Get item display string
     *
     * @param \App\HHH_Library\general\php\Enums\LocaleEnum $locale
     * @return ?string
     */
    public function translate(LocaleEnum $locale = null): ?string
    {
        return __('thisApp.Enum.ChatbotFilterTypes.' . $this->name);
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
            self::ClientCategory => [
                self::KEY_ALLOWED_CATEGOLRIES => [],
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

            case self::ClientCategory:
                if (is_array($value) && !Arr::isList($value)) {

                    /**
                     * The $value is comming from HTML form and
                     * selected itesm must be converted to list
                     */
                    $allowedCategories = [];
                    foreach ($value as $key => $val) {
                        if (CastEnum::Boolean->cast($val))
                            array_push($allowedCategories, $key);
                    }
                    return $allowedCategories;
                }
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

        $userRoleId = -1; // Guest User

        if (auth()->check()) {
            $user = User::authUser();
            $userRoleId = $user[UsersTableEnum::RoleId->dbName()];
        }

        $isPassed = false;

        // ClientCategory
        if ($this == self::ClientCategory) {

            if (in_array($userRoleId, $data[self::KEY_ALLOWED_CATEGOLRIES]))
                $isPassed = true;
        }

        if ($isPassed) {
            return  [
                self::KEY_TYPE  => $this->name,
                self::KEY_DATA  => [
                    self::KEY_IS_PASSED => true,
                ],
            ];
        }

        return null;
    }
}
