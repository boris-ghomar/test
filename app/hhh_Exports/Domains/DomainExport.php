<?php

namespace App\hhh_Exports\Domains;

use App\HHH_Library\Export\SuperClasses\SuperExport;
use App\Http\Resources\Export\Domains\DomainExportCollection;
use App\Models\BackOffice\Domains\Domain;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DomainExport extends SuperExport
{

    /******************** Implements ********************/

    /**
     * Specify the name of the data sheet in this function.
     *
     * @return string
     */
    public function sheetName(): string
    {
        return trans('bo_sidebar.Domains.Domains', [], 'en');
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
        return Domain::ApiIndexCollection($filterData);
    }

    /**
     * This function returns column titles .
     *
     * @return array
     */
    public function titleRow(): array
    {
        return [
            __('general.Name'),
            __('general.Status'),
            __('thisApp.AdminPages.Domains.domainCategory'),
            __('thisApp.AdminPages.DomainsHoldersAccounts.domainHolder'),
            __('thisApp.AdminPages.DomainsHoldersAccounts.domainHolderAccount'),
            __('thisApp.AdminPages.Domains.autoRenew'),
            __('thisApp.AdminPages.Domains.Public'),
            __('thisApp.AdminPages.Domains.SuspiciousClients'),
            __('thisApp.AdminPages.Domains.Reported'),
            __('thisApp.AdminPages.Domains.registeredAt'),
            __('thisApp.AdminPages.Domains.expiresAt'),
            __('thisApp.AdminPages.Domains.announcedAt'),
            __('thisApp.AdminPages.Domains.blockedAt'),
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
        return new DomainExportCollection(
            $this->getDataModelRelation()->get()
        );
    }

    /******************** Implements ********************/
}
