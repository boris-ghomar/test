<?php

namespace App\Http\Resources\General;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\NotificationsTableEnum as TableEnum;
use App\Http\Resources\ApiResponseResource;

class NotificationResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $notificationHandler = $this[TableEnum::Type->dbName()];

        return [
            TableEnum::Id->dbName()             => $this[TableEnum::Id->dbName()],
            TimestampsEnum::CreatedAt->dbName() => $this[TimestampsEnum::CreatedAt->dbName()],
            'subject'                           => $notificationHandler::getSubject(),
            'message'                           => $notificationHandler::getMessage($this[TableEnum::Id->dbName()]),
        ];
    }
}
