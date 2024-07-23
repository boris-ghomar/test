<?php

namespace App\hhh_Exports\Referral;

use App\HHH_Library\Export\SuperClasses\SuperExport;
use App\Http\Resources\Export\Referral\ReferralExportCollection;
use App\Models\BackOffice\Referral\Referral;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReferralExport extends SuperExport
{

    /******************** Implements ********************/

    /**
     * Specify the name of the data sheet in this function.
     *
     * @return string
     */
    public function sheetName(): string
    {
        return trans('bo_sidebar.Referral.ReferralsManagement', [], 'en');
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
        return Referral::ApiIndexCollection($filterData);
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
            sprintf('%s (%s)', __('thisApp.UserId'), __('thisApp.AdminPages.Referral.Referrer')),
            sprintf('%s (%s)', __('thisApp.BetconstructId'), __('thisApp.AdminPages.Referral.Referrer')),
            sprintf('%s (%s)', __('general.UserName'), __('thisApp.AdminPages.Referral.Referrer')),
            __('general.CreatedAt'),

        ];
    }

    /**
     * This function returns a collection of requested data.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function dataCollection(): ResourceCollection
    {
        return new ReferralExportCollection(
            $this->getDataModelRelation()->get()
        );
    }

    /******************** Implements ********************/
}
