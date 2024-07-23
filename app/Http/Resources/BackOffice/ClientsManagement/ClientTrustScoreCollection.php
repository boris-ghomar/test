<?php

namespace App\Http\Resources\BackOffice\ClientsManagement;

use App\Http\Resources\ApiResponseCollection;

class ClientTrustScoreCollection extends ApiResponseCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = ClientTrustScoreResource::class;

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
