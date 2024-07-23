<?php

namespace App\hhh_Exports\Domains;

use App\HHH_Library\Export\SuperClasses\SuperExport;
use App\Http\Resources\Export\Domains\DomainHolderAccountExportCollection;
use App\Models\BackOffice\Domains\DomainHolderAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DomainHolderAccountExport extends SuperExport
{

    /******************** Implements ********************/

    /**
     * Specify the name of the data sheet in this function.
     *
     * @return string
     */
    public function sheetName(): string
    {
        return trans('bo_sidebar.Domains.HoldersAccounts', [], 'en');
    }

    /**
     * This function returns the data model collection.
     *
     * @param array $filterData
     * @return \Illuminate\Database\Eloquent\Builder
     * Exp:: return DomainHolderAccount::ApiIndexCollection($filterData);
     */
    public function dataModelRelation(array $filterData): Builder
    {

        return DomainHolderAccount::ApiIndexCollection($filterData);
    }

    /**
     * This function returns column titles .
     *
     * @return array
     */
    public function titleRow(): array
    {
        return [
            __('thisApp.AdminPages.DomainsHoldersAccounts.domainHolder'),
            __('general.URL'),
            __('general.UserName'),
            __('general.Email'),
            __('general.isActive'),
            __('general.Description'),
        ];
    }

    /**
     * This function returns a collection of requested data.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function dataCollection(): ResourceCollection
    {
        return new DomainHolderAccountExportCollection(
            $this->getDataModelRelation()->get()
        );
    }

    /******************** Implements ********************/
}
