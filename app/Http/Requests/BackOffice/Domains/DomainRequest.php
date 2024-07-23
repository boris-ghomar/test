<?php

namespace App\Http\Requests\BackOffice\Domains;

use App\Enums\Database\DatabaseTablesEnum;
use App\Enums\Database\Tables\DomainHolderAccountsTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\Http\Requests\SuperClasses\SuperRequest;
use App\Models\BackOffice\Domains\Domain as model;
use App\Models\BackOffice\Domains\DomainCategory;
use App\Models\BackOffice\Domains\DomainHolderAccount;
use App\Models\User;
use App\Rules\General\Database\ExistsItem;
use App\Rules\General\StringPattern\EnglishString;
use App\Rules\General\User\DateTimeFormatRule;
use Illuminate\Validation\Rule;

class DomainRequest extends SuperRequest
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
                'bail', 'required',
                new EnglishString,
                Rule::unique(DatabaseTablesEnum::Domains->tableName(), TableEnum::Name->dbName())->ignore($this->id, 'id'),
            ],

            TableEnum::DomainCategoryId->dbName() => ['bail', 'required', 'numeric', new ExistsItem(DomainCategory::class)],
            TableEnum::DomainHolderAccountId->dbName() => ['bail', 'required', 'numeric', new ExistsItem(DomainHolderAccount::class)],

            TableEnum::AutoRenew->dbName() => ['boolean'],
            TableEnum::Status->dbName() => [
                'required',
                Rule::in(DomainStatusEnum::names()),
            ],

            TableEnum::Public->dbName() => ['boolean'],
            TableEnum::Reported->dbName() => ['boolean'],

            TableEnum::RegisteredAt->dbName() => ['bail', 'nullable', new DateTimeFormatRule],
            TableEnum::ExpiresAt->dbName() => ['bail', 'nullable', new DateTimeFormatRule],
            TableEnum::AnnouncedAt->dbName() => ['bail', 'nullable', new DateTimeFormatRule],
            TableEnum::BlockedAt->dbName() => ['bail', 'nullable', new DateTimeFormatRule],
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
                TableEnum::Public->dbName()                 => trans('thisApp.AdminPages.Domains.Public'),
                TableEnum::Reported->dbName()               => trans('thisApp.AdminPages.Domains.Reported'),
                TableEnum::Descr->dbName()                  => trans('general.Description'),
                TableEnum::RegisteredAt->dbName()           => trans('thisApp.AdminPages.Domains.registeredAt'),
                TableEnum::ExpiresAt->dbName()              => trans('thisApp.AdminPages.Domains.expiresAt'),
                TableEnum::AnnouncedAt->dbName()            => trans('thisApp.AdminPages.Domains.announcedAt'),
                TableEnum::BlockedAt->dbName()              => trans('thisApp.AdminPages.Domains.blockedAt'),

                DomainHolderAccountsTableEnum::DomainHolderId->dbName()              => trans('thisApp.AdminPages.DomainsHoldersAccounts.domainHolder'),
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
            TableEnum::AutoRenew->dbName()  =>  CastEnum::Boolean->cast($this[TableEnum::AutoRenew->dbName()]),
            TableEnum::Public->dbName()     =>  CastEnum::Boolean->cast($this[TableEnum::Public->dbName()]),
            TableEnum::Reported->dbName()   =>  CastEnum::Boolean->cast($this[TableEnum::Reported->dbName()]),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $user = User::authUser();

        $this->merge([

            TableEnum::RegisteredAt->dbName()   => $user->convertLocalTimeToUTC($this[TableEnum::RegisteredAt->dbName()]),
            TableEnum::ExpiresAt->dbName()      => $user->convertLocalTimeToUTC($this[TableEnum::ExpiresAt->dbName()]),
            TableEnum::AnnouncedAt->dbName()    => $user->convertLocalTimeToUTC($this[TableEnum::AnnouncedAt->dbName()]),
            TableEnum::BlockedAt->dbName()      => $user->convertLocalTimeToUTC($this[TableEnum::BlockedAt->dbName()]),
        ]);
    }
}
