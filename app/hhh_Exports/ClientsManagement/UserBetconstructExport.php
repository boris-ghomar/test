<?php

namespace App\hhh_Exports\ClientsManagement;

use App\HHH_Library\Export\SuperClasses\SuperExport;
use App\Http\Controllers\BackOffice\ClientsManagement\UserBetconstructController;
use App\Http\Resources\Export\ClientsManagement\UserBetconstructExportCollection;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserBetconstructExport extends SuperExport
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
        return trans('bo_sidebar.BetconstructClients.Clients', [], 'en');
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
        return UserBetconstruct::ApiIndexCollection($filterData);
    }

    /**
     * This function returns column titles .
     *
     * @return array
     */
    public function titleRow(): array
    {
        $customizablePageSettings = (new UserBetconstructController)->getCustomizablePageSettings();
        $requiredColumns = $customizablePageSettings[config('hhh_config.keywords.requiredColumns')];
        $selectableColumns = $customizablePageSettings[config('hhh_config.keywords.selectableColumns')];
        $selectedColumns = $customizablePageSettings[config('hhh_config.keywords.selectedColumns')];

        $titles = array_values($requiredColumns);

        foreach ($selectedColumns as $selectedColumn) {
            array_push($titles, $selectableColumns[$selectedColumn]);
        }

        return $titles;
    }

    /**
     * This function returns a collection of requested data.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function dataCollection(): ResourceCollection
    {
        return new UserBetconstructExportCollection(
            $this->getDataModelRelation()->get()
        );
    }

    /******************** Implements ********************/
}
