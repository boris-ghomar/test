<?php

namespace App\Http\Resources\Export\ClientsManagement;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserBetconstructExportCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = UserBetconstructExportResource::class;

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
