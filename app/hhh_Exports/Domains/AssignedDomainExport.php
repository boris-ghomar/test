<?php

namespace App\hhh_Exports\Domains;

use App\HHH_Library\Export\SuperClasses\SuperExport;
use App\Http\Resources\Export\Domains\AssignedDomainExportCollection;
use App\Models\BackOffice\Domains\AssignedDomain;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AssignedDomainExport extends SuperExport
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
        return trans('bo_sidebar.Domains.AssignedDomains', [], 'en');
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
        return AssignedDomain::ApiIndexCollection($filterData);
    }

    /**
     * This function returns column titles .
     *
     * @return array
     */
    public function titleRow(): array
    {
        return [
            __('thisApp.UserId'),
            __('general.UserName'),
            __('general.Domain'),
            __('thisApp.AdminPages.Domains.ClientTrustScoreAssignment'),
            __('thisApp.AdminPages.Domains.ClientTrustScoreCurrent'),
            __('thisApp.AdminPages.Domains.DomainSuspiciousAssignment'),
            __('thisApp.AdminPages.Domains.DomainSuspiciousCurrent'),
            __('general.Status'),
            __('thisApp.AdminPages.Domains.Public'),
            __('thisApp.AdminPages.Domains.SuspiciousClients'),
            __('thisApp.AdminPages.Domains.Reported'),
            __('thisApp.AdminPages.Domains.FakeAssigned'),
            __('thisApp.AdminPages.Domains.announcedAt'),
            __('thisApp.AdminPages.Domains.assignedAt'),
            __('thisApp.AdminPages.Domains.reportedAt'),
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
        return new AssignedDomainExportCollection(
            $this->getDataModelRelation()->get()
        );
    }

    /******************** Implements ********************/
}
