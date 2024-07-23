<?php

namespace App\Http\Resources\Export\Domains;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DomainHolderAccountExportCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = DomainHolderAccountExportResource::class;

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
