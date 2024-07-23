<?php

namespace App\Http\Resources\BackOffice\PostGrouping;

use App\Http\Resources\ApiResponseCollection;

class PostGroupsDisplayPositionCollection extends ApiResponseCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = PostGroupsDisplayPositionResource::class;

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
