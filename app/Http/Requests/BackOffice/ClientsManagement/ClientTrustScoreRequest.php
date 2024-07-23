<?php

namespace App\Http\Requests\BackOffice\ClientsManagement;

use App\Enums\Database\Tables\ClientTrustScoresTableEnum as TableEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\ClientsManagement\ClientTrustScore as model;

class ClientTrustScoreRequest extends SuperRequest
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
        // Disabled in contorller
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

            TableEnum::Score->dbName() => [
                'bail',
                'required', 'numeric', 'max:100'
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
        // Disabled in contorller
        return [];
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
                TableEnum::Score->dbName()  => trans('thisApp.AdminPages.ClientsManagement.TrustScore'),
                TableEnum::Descr->dbName()  => trans('general.Description'),
            ]
        );
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            TableEnum::Score->dbName() =>  number_format($this[TableEnum::Score->dbName()], 0, "", ""),
        ]);
    }
}
