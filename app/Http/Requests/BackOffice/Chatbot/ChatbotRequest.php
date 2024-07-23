<?php

namespace App\Http\Requests\BackOffice\Chatbot;

use App\Enums\Database\Tables\ChatbotsTableEnum as TableEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Chatbot\Chatbot as model;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\Database\UniqueInModel;

class ChatbotRequest extends SuperRequest
{

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
            TableEnum::Name->dbName() => [
                'required',
                new UniqueInModel(model::class, $this->input(TableEnum::Id->dbName()))
            ],
            TableEnum::IsActive->dbName() => ['required', 'bool'],
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
                TableEnum::Name->dbName()       => trans('general.Name'),
                TableEnum::IsActive->dbName()   => trans('thisApp.PostActions.Comment'),
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
        $this->merge([
            TableEnum::IsActive->dbName() =>  TableEnum::IsActive->cast($this->input(TableEnum::IsActive->dbName())),
        ]);
    }
}
