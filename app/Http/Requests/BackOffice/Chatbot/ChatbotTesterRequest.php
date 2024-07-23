<?php

namespace App\Http\Requests\BackOffice\Chatbot;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\ChatbotTestersTableEnum as TableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Chatbot\Chatbot;
use App\Models\BackOffice\Chatbot\ChatbotTester as model;
use App\Rules\General\Database\ExistsInModel;
use App\Rules\General\Database\ExistsItem;
use Illuminate\Validation\Rule;

class ChatbotTesterRequest extends SuperRequest
{

    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->defaultAuthorize(model::class);
    }

    /******************** Action rules *********************/

    /**
     * Rules for store a newly created resource in storage.
     *
     * @return array
     */
    public function rulesStore(): array
    {
        return [
            TableEnum::UserId->dbName() => [
                'required',
                new ExistsInModel(BetconstructClient::class, ClientModelEnum::UserId->dbName()),
                Rule::unique(DatabaseTablesEnum::ChatbotTesters->tableName())->ignore($this->id)
            ],

            TableEnum::ChatbotId->dbName() => [
                'required',
                new ExistsItem(Chatbot::class, __('thisApp.Errors.Chatbot.ChatbotNotFound')),
            ],
        ];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        return $this->rulesStore();
    }

    /**
     * Rules for remove the specified resource from storage.
     *
     * @return array
     */
    public function rulesDestroy(): array
    {
        return [
            TableEnum::Id->dbName() => [new ExistsItem(model::class)],
        ];
    }

    /******************** Action rules END *********************/

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return $this->addPadToArrayVal(
            [
                TableEnum::UserId->dbName()     => trans('thisApp.UserId'),
                TableEnum::ChatbotId->dbName()  => trans('thisApp.AdminPages.Chatbot.ChatbotName'),
            ]
        );
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        //
    }
}
