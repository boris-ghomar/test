<?php

namespace App\Http\Resources\BackOffice\Chatbot;


use App\Enums\Database\Tables\ChatbotTestersTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class ChatbotTesterResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            TableEnum::Id->dbName()         => $this[TableEnum::Id->dbName()],
            TableEnum::UserId->dbName()     => (int) $this[TableEnum::UserId->dbName()],
            TableEnum::ChatbotId->dbName()  => (int) $this[TableEnum::ChatbotId->dbName()],
            'bc_username'                   => $this->bc_username,
        ];
    }
}
