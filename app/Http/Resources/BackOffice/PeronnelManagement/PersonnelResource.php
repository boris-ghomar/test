<?php

namespace App\Http\Resources\BackOffice\PeronnelManagement;

use App\Enums\Database\Tables\PersonnelExtrasTableEnum as UserExtra;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Http\Resources\ApiResponseResource;

class PersonnelResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            UsersTableEnum::Id->dbName()            => (int) $this[UsersTableEnum::Id->dbName()],
            UsersTableEnum::Username->dbName()      => $this[UsersTableEnum::Username->dbName()],
            UsersTableEnum::Email->dbName()         => $this[UsersTableEnum::Email->dbName()],
            UsersTableEnum::RoleId->dbName()        => (int) $this[UsersTableEnum::RoleId->dbName()],
            UsersTableEnum::Status->dbName()        => $this[UsersTableEnum::Status->dbName()],

            UserExtra::FirstName->dbName()          => $this[UserExtra::FirstName->dbName()],
            UserExtra::LastName->dbName()           => $this[UserExtra::LastName->dbName()],
            UserExtra::AliasName->dbName()          => $this[UserExtra::AliasName->dbName()],
            UserExtra::Gender->dbName()             => $this[UserExtra::Gender->dbName()],
            UserExtra::Descr->dbName()              => $this[UserExtra::Descr->dbName()],

        ];
    }
}
