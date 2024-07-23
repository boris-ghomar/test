<?php

namespace App\Http\Resources\BackOffice\Chatbot;


use App\Enums\Database\Tables\ChatbotsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class ChatbotResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $editBtn = sprintf(
            '<a target="_blank" href="%s" type="button" class="btn btn-outline-primary">%s</a>',
            $this->EditUrl,
            __('general.buttons.Edit')
        );

        return [
            TableEnum::Id->dbName()         => (int) $this[TableEnum::Id->dbName()],
            TableEnum::Name->dbName()       => $this[TableEnum::Name->dbName()],
            TableEnum::Descr->dbName()      => $this[TableEnum::Descr->dbName()],
            TableEnum::IsActive->dbName()   => $this[TableEnum::IsActive->dbName()],

            'edit_btn'                      => $editBtn,
        ];
    }
}
