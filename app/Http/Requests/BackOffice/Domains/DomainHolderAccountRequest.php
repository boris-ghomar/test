<?php

namespace App\Http\Requests\BackOffice\Domains;

use App\Enums\Database\Tables\DomainHolderAccountsTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Domains\DomainHolder;
use App\Models\BackOffice\Domains\DomainHolderAccount as model;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\Database\UniqueSuperKey;
use App\Rules\General\StringPattern\EnglishString;

class DomainHolderAccountRequest extends SuperRequest
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
            TableEnum::DomainHolderId->dbName() => ['bail', 'required', 'numeric', new ExistsItem(DomainHolder::class)],

            TableEnum::Username->dbName() => [
                'bail', 'required',
                new EnglishString,
                new UniqueSuperKey(model::class, $this->id, [
                    TableEnum::DomainHolderId->dbName() => $this->domain_holder_id,
                    TableEnum::Username->dbName()       => $this->username,
                ])
            ],

            TableEnum::Email->dbName() => ['nullable', 'email:rfc,dns'],
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
            TableEnum::Id->dbName()    => [new existsItem(model::class)],
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
                TableEnum::DomainHolderId->dbName() => trans('thisApp.AdminPages.DomainsHoldersAccounts.domainHolder'),
                TableEnum::Username->dbName()       => trans('general.UserName'),
                TableEnum::Email->dbName()          => trans('general.Email'),
                TableEnum::IsActive->dbName()       => trans('general.isActive'),
                TableEnum::Descr->dbName()          => trans('general.Description'),
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
