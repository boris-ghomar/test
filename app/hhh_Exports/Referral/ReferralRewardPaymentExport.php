<?php

namespace App\hhh_Exports\Referral;

use App\HHH_Library\Export\SuperClasses\SuperExport;
use App\Http\Resources\Export\Referral\ReferralRewardPaymentExportCollection;
use App\Models\BackOffice\Referral\ReferralRewardPayment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReferralRewardPaymentExport extends SuperExport
{

    /******************** Implements ********************/

    /**
     * Specify the name of the data sheet in this function.
     *
     * @return string
     */
    public function sheetName(): string
    {
        return trans('bo_sidebar.Referral.RewardPayments', [], 'en');
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
        return ReferralRewardPayment::ApiIndexCollection($filterData);
    }

    /**
     * This function returns column titles .
     *
     * @return array
     */
    public function titleRow(): array
    {
        return [
            __('general.ID'),
            __('thisApp.UserId'),
            __('thisApp.BetconstructId'),
            __('general.UserName'),
            __('thisApp.AdminPages.Referral.RewardName'),
            __('general.amount'),
            __('bc_api.CurrencyId'),
            __('thisApp.AdminPages.Referral.IsPaymentProcessDone'),
            __('thisApp.AdminPages.Referral.IsPaymentSuccessful'),
            __('general.CreatedAt'),
            __('thisApp.SystemMessage'),
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
        return new ReferralRewardPaymentExportCollection(
            $this->getDataModelRelation()->get()
        );
    }

    /******************** Implements ********************/
}
