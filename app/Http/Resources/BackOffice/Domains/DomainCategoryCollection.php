<?php

namespace App\Http\Resources\BackOffice\Domains;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\ApiResponseCollection;

class DomainCategoryCollection extends ApiResponseCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = DomainCategoryResource::class;

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
