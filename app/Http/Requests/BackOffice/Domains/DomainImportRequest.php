<?php

namespace App\Http\Requests\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DomainHolderAccountsTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Domains\DomainCategory;
use App\Models\BackOffice\Domains\DomainHolderAccount;
use App\Models\BackOffice\Domains\DomainImport as model;
use App\Rules\General\Database\ExistsItem;

class DomainImportRequest extends SuperRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can(PermissionAbilityEnum::viewAny->name, model::class);
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

            TableEnum::DomainCategoryId->dbName() => ['bail', 'required', 'numeric', new ExistsItem(DomainCategory::class)],
            TableEnum::DomainHolderAccountId->dbName() => ['bail', 'required', 'numeric', new ExistsItem(DomainHolderAccount::class)],

            "Overwrite" => ['bail', 'required', 'boolean'],
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
            TableEnum::Id->dbName()    => [new ExistsItem(model::class)],
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
                TableEnum::Name->dbName()                   => trans('general.Name'),
                TableEnum::DomainCategoryId->dbName()       => trans('thisApp.AdminPages.Domains.domainCategory'),
                TableEnum::DomainHolderAccountId->dbName()  => trans('thisApp.AdminPages.DomainsHoldersAccounts.domainHolderAccount'),
                TableEnum::AutoRenew->dbName()              => trans('thisApp.AdminPages.Domains.autoRenew'),
                TableEnum::Status->dbName()                 => trans('general.Status'),
                TableEnum::Descr->dbName()                  => trans('general.Description'),
                TableEnum::RegisteredAt->dbName()           => trans('thisApp.AdminPages.Domains.registeredAt'),
                TableEnum::ExpiresAt->dbName()              => trans('thisApp.AdminPages.Domains.expiresAt'),
                TableEnum::AnnouncedAt->dbName()            => trans('thisApp.AdminPages.Domains.announcedAt'),
                TableEnum::BlockedAt->dbName()              => trans('thisApp.AdminPages.Domains.blockedAt'),

                DomainHolderAccountsTableEnum::DomainHolderId->dbName()              => trans('thisApp.AdminPages.DomainsHoldersAccounts.domainHolder'),
                "Overwrite" => trans('general.Overview'),
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
            "Overwrite"                     =>  CastEnum::Boolean->cast($this->Overwrite),
        ]);
    }
}
