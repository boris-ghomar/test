<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;


class ApiResponseCollection extends ResourceCollection
{

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'status' => "success",
            $this->mergeWhen(!empty($request->warnings), ['warnings' => $request->warnings]),
            'data' => $this->collection,
        ];
    }
}
