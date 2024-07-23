<?php

namespace App\Http\Resources\Export\Referral;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReferralRewardPaymentExportCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = ReferralRewardPaymentExportResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
