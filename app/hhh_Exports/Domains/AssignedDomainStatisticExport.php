<?php

namespace App\hhh_Exports\Domains;

use App\HHH_Library\Export\SuperClasses\SuperExport;
use App\Http\Resources\Export\Domains\AssignedDomainStatisticExportCollection;
use App\Models\BackOffice\Domains\AssignedDomainsStatistic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AssignedDomainStatisticExport extends SuperExport
{

    /**
     * @override parent
     * Max allowed records count per export
     *
     * @return int
     */
    protected function maxAllowedRecordsCounts(): int
    {
        return 1000;
    }

    /******************** Implements ********************/

    /**
     * Specify the name of the data sheet in this function.
     *
     * @return string
     */
    public function sheetName(): string
    {
        return trans('bo_sidebar.Domains.AssignedDomainsStatistics', [], 'en');
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
        return AssignedDomainsStatistic::ApiIndexCollection($filterData);
    }

    /**
     * This function returns column titles .
     *
     * @return array
     */
    public function titleRow(): array
    {
        return [
            __('general.Domain'),
            __('thisApp.AdminPages.ClientsManagement.TrustScore'),
            __('general.Status'),
            __('thisApp.AdminPages.Domains.ClientsCount'),
            __('thisApp.AdminPages.Domains.Public'),
            __('thisApp.AdminPages.Domains.SuspiciousClients'),
            __('thisApp.AdminPages.Domains.Reported'),
            __('thisApp.AdminPages.Domains.ReportsCount'),
            __('thisApp.AdminPages.Domains.FakeAssigned'),
            __('thisApp.AdminPages.Domains.announcedAt'),
            __('thisApp.AdminPages.Domains.blockedAt'),
        ];
    }

    /**
     * This function returns a collection of requested data.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function dataCollection(): ResourceCollection
    {
        return new AssignedDomainStatisticExportCollection(
            $this->getDataModelRelation()->get()
        );
    }

    /******************** Implements ********************/
}
