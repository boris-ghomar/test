<?php

namespace App\Http\Requests\BackOffice\Domains;

use App\Enums\Database\Tables\DomainHoldersTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Domains\DomainHolder as model;
use App\Rules\BackOffice\Domains\DomainHolder\HasDomainHolderAccount;
use App\Rules\General\Database\ExistsItem;
use Illuminate\Validation\Rule;

class DomainHolderRequest extends SuperRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
                Rule::unique(model::class)->ignore($this->id, 'id'),
            ],
            TableEnum::Url->dbName() => ['required'],
            TableEnum::IsActive->dbName() => ['boolean'],
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
            TableEnum::Id->dbName()    => [new ExistsItem(model::class), new HasDomainHolderAccount],
        ];
    }

    /******************** Action rules END *********************/

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->addPadToArrayVal(
            [
                TableEnum::Name->dbName()       => trans('general.Name'),
                TableEnum::Url->dbName()        => trans('general.URL'),
                TableEnum::IsActive->dbName()   => trans('general.isActive'),
                TableEnum::Descr->dbName()      => trans('general.Description'),
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
            TableEnum::IsActive->dbName() =>  CastEnum::Boolean->cast($this[TableEnum::IsActive->dbName()]),
        ]);
    }
}
