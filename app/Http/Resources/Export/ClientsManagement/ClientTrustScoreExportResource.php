<?php

namespace App\Http\Resources\Export\ClientsManagement;

use App\Enums\Database\Tables\ClientTrustScoresTableEnum as TableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\Export\Traits\FormatExcelColumns;
use App\HHH_Library\general\php\ArrayHelper;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Http\Resources\ApiResponseResource;
use App\Models\BackOffice\ClientsManagement\ClientCategory;

class ClientTrustScoreExportResource extends ApiResponseResource
{
    use FormatExcelColumns;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $clientCategories = DropdownListCreater::makeByModel(ClientCategory::class, RolesTableEnum::Name->dbName())
            ->get();

        return [
            $this->cellStyleCenter($this[TableEnum::UserId->dbName()]),
            $this->cellStyleCenter($this->betconstruct_id),
            $this->cellStyleLeft($this->username),
            $this->cellStyleCenter(ArrayHelper::search($this[UsersTableEnum::RoleId->dbName()], $clientCategories)),
            $this->cellStyleCenter($this[TableEnum::Score->dbName()]),
            $this->cellStyleCenter($this[TableEnum::DomainSuspicious->dbName()]),
            $this->cellStyleCenter(number_format($this[TableEnum::DepositCount->dbName()], 0)),
            $this->cellStyleCenter(number_format($this[TableEnum::Balance->dbName()], 2)),
            $this->cellStyleCenter($this[ClientModelEnum::CurrencyId->dbName()]),
            $this->cellStyleLeft($this[TableEnum::Descr->dbName()]),
        ];
    }
}
