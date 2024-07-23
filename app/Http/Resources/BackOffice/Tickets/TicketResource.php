<?php

namespace App\Http\Resources\BackOffice\Tickets;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\TicketsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Http\Resources\ApiResponseResource;

class TicketResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (isset($this['answering_btn'])) {
            // Data is comming from controller update method
            $answeringBtn = $this['answering_btn'];
        } else {

            $answeringBtn = sprintf(
                '<a target="_blank" href="%s" type="button" class="btn btn-outline-primary">%s</a>',
                $this[TableEnum::AnsweringUrl->dbName()],
                __('thisApp.Buttons.Answering')
            );
        }



        return [
            TableEnum::Id->dbName()             => (int) $this[TableEnum::Id->dbName()],
            TableEnum::OwnerId->dbName()        => (int) $this[TableEnum::OwnerId->dbName()],
            TableEnum::Priority->dbName()       => $this[TableEnum::Priority->dbName()],
            TableEnum::Subject->dbName()        => $this[TableEnum::Subject->dbName()],
            TableEnum::Status->dbName()         => $this[TableEnum::Status->dbName()],

            TableEnum::PrivateNote->dbName()    => $this[TableEnum::PrivateNote->dbName()],

            TimestampsEnum::CreatedAt->dbName() => $this[TimestampsEnum::CreatedAt->dbName()],

            'client_category_id'                => (int) $this['client_category_id'],

            'personnel_username'                => $this['personnel_username'],

            'betconstruct_id'                   => (int) $this['betconstruct_id'],
            'betconstruct_username'             => $this['betconstruct_username'],

            'answering_btn'                     => $answeringBtn,
        ];
    }
}
