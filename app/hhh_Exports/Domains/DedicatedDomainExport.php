<?php

namespace App\hhh_Exports\Domains;

use App\HHH_Library\Export\SuperClasses\SuperExport;
use App\Http\Resources\Export\Domains\DedicatedDomainExportCollection;
use App\Models\BackOffice\Domains\DedicatedDomain;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DedicatedDomainExport extends SuperExport
{

    /******************** Implements ********************/

    /**
     * Specify the name of the data sheet in this function.
     *
     * @return string
     */
    public function sheetName(): string
    {
        return trans('bo_sidebar.Domains.DedicatedDomains', [], 'en');
    }

    /**
     * This function returns the data model collection.
     *
     * @param array $filterData
     * @return \Illuminate\Database\Eloquent\Builder
     * Exp:: return ReportedDomain::ApiIndexCollection($filterData);
     */
    public function dataModelRelation(array $filterData): Builder
    {
        return DedicatedDomain::ApiIndexCollection($filterData)
            ->limit(parent::MAX_ALLOWED_EXPORT_RECORDS_COUNT);
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
            __('general.Domain'),
            __('thisApp.AdminPages.Domains.DesktopVerion'),
            __('thisApp.AdminPages.Domains.MobileVerion'),
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
        return new DedicatedDomainExportCollection(
            $this->getDataModelRelation()->get()
        );
    }

    /******************** Implements ********************/
}
