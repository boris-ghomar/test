<?php

namespace App\Http\Requests\BackOffice\Tickets;

use App\Enums\Database\Tables\TicketsTableEnum as TableEnum;
use App\Enums\Tickets\TicketPrioritiesEnum;
use App\Enums\Tickets\TicketsStatusEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Tickets\Ticket as model;
use App\Rules\General\Database\ExistsItem;
use Illuminate\Validation\Rule;

class TicketRequest extends SuperRequest
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
        return [];
    }

    /**
     * Rules for update the specified resource in storage.
     *
     * @return array
     */
    public function rulesUpdate(): array
    {
        return [

            TableEnum::Priority->dbName() => [
                'required',
                Rule::in(TicketPrioritiesEnum::names()),
            ],

            TableEnum::Status->dbName() => [
                'required',
                Rule::in(TicketsStatusEnum::names()),
            ],
        ];
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
                TableEnum::Priority->dbName()   => trans('thisApp.Priority'),
                TableEnum::Subject->dbName()    => trans('general.Subject'),
                TableEnum::Status->dbName()     => trans('general.Status'),
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
            //
        ]);
    }
}
