<?php

namespace App\hhh_Exports\ClientsManagement;

use App\HHH_Library\Export\SuperClasses\SuperExport;
use App\Http\Resources\Export\ClientsManagement\ClientTrustScoreExportCollection;
use App\Models\BackOffice\ClientsManagement\ClientTrustScore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientTrustScoreExport extends SuperExport
{

    /**
     * @override parent
     * Max allowed records count per export
     *
     * @return int
     */
    protected function maxAllowedRecordsCounts(): int
    {
        return 3000;
    }

    /******************** Implements ********************/

    /**
     * Specify the name of the data sheet in this function.
     *
     * @return string
     */
    public function sheetName(): string
    {
        return trans('bo_sidebar.BetconstructClients.ClientTrustScores', [], 'en');
    }

    /**
     * This function returns the data model collection.
     *
     * @param array $filterData
     * @return \Illuminate\Database\Eloquent\Builder
     * Exp:: return ClientTrustScore::ApiIndexCollection($filterData);
     */
    public function dataModelRelation(array $filterData): Builder
    {
        return ClientTrustScore::ApiIndexCollection($filterData);
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
            __('thisApp.BetconstructId'),
            __('general.UserName'),
            __("general.Category"),
            __('thisApp.AdminPages.ClientsManagement.TrustScore'),
            __('thisApp.AdminPages.ClientsManagement.DomainSuspicious'),
            __('bc_api.DepositCount'),
            __('bc_api.Balance'),
            __('bc_api.CurrencyId'),
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
        return new ClientTrustScoreExportCollection(
            $this->getDataModelRelation()->get()
        );
    }

    /******************** Implements ********************/
}
