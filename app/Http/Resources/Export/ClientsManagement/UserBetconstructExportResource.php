<?php

namespace App\Http\Resources\Export\ClientsManagement;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Users\ClientProfileCheckEnum;
use App\Enums\Users\ContactMethodsEnum;
use App\Enums\Users\UsersStatusEnum;
use App\HHH_Library\Export\Traits\FormatExcelColumns;
use App\HHH_Library\general\php\JsonHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\GendersEnum as ExternalAdminGendersEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ClientSwarmModelEnum;
use App\Http\Controllers\BackOffice\ClientsManagement\UserBetconstructController;
use App\Http\Resources\ApiResponseResource;
use App\Models\User;

class UserBetconstructExportResource extends ApiResponseResource
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
        $customizablePageSettings = (new UserBetconstructController)->getCustomizablePageSettings();
        $displayColumns = $customizablePageSettings[config('hhh_config.keywords.displayColumns')];

        $res = [];
        $attributes = $this->getAttributes();
        foreach ($this->itemsList() as $key => $func) {

            if (in_array($key, $displayColumns))
                $res[$key] = $func($this[$key], $attributes);
        }

        return $res;
    }


    /**
     * Get attributes list
     *
     * @return array
     */
    private function itemsList(): array
    {
        $authUser = User::authUser();

        /** @var User $user */
        $recordUser = User::find($this[UsersTableEnum::Id->dbName()]);

        return [
            UsersTableEnum::Id->dbName()                => fn ($value) => $this->cellStyleCenter($value),
            UsersTableEnum::RoleId->dbName()            => fn ($value, $attributes) => $this->cellStyleCenter($attributes['client_category_name']),
            UsersTableEnum::Status->dbName()            => fn ($value) => $this->cellStyleCenter(UsersStatusEnum::getCase($value)->translate()),

            ClientModelEnum::Login->dbName()                    => fn ($value) => $this->cellStyleLeft($value),
            'betconstruct_id'                                   => fn ($value) => $this->cellStyleCenter($value),
            ClientModelEnum::Email->dbName()                    => fn ($value) => $this->cellStyleLeft($value),
            ClientModelEnum::FirstName->dbName()                => fn ($value) => $this->cellStyleCenter($value),
            ClientModelEnum::LastName->dbName()                 => fn ($value) => $this->cellStyleCenter($value),

            ClientModelEnum::Gender->dbName()                   => fn ($value) => $this->cellStyleCenter(ExternalAdminGendersEnum::getCaseByValue($value)->translate()),
            ClientModelEnum::Phone->dbName()                    => fn ($value) => $this->cellStyleLeft($value),
            ClientModelEnum::MobilePhone->dbName()              => fn ($value) => $this->cellStyleLeft($value),
            ClientModelEnum::BirthDateStamp->dbName()           => fn ($value) => $this->cellStyleCenter($authUser->convertUTCToLocalTime($value, true)),
            ClientModelEnum::RegionCode->dbName()               => fn ($value) => $this->cellStyleCenter($value),
            ClientModelEnum::CurrencyId->dbName()               => fn ($value) => $this->cellStyleCenter($value),
            ClientModelEnum::Balance->dbName()                  => fn ($value) => $this->cellStyleCenter(number_format($value, 2)),
            ClientModelEnum::UnplayedBalance->dbName()          => fn ($value) => $this->cellStyleCenter(number_format($value, 2)),
            ClientModelEnum::LastLoginIp->dbName()              => fn ($value) => $this->cellStyleLeft($value),
            ClientModelEnum::LastLoginTimeStamp->dbName()       => fn ($value) => $this->cellStyleCenter($authUser->convertUTCToLocalTime($value, true)),
            ClientModelEnum::City->dbName()                     => fn ($value) => $this->cellStyleCenter($value),
            ClientModelEnum::CreatedStamp->dbName()             => fn ($value) => $this->cellStyleCenter($authUser->convertUTCToLocalTime($value, true)),
            ClientModelEnum::ModifiedStamp->dbName()            => fn ($value) => $this->cellStyleCenter($authUser->convertUTCToLocalTime($value, true)),
            ClientSwarmModelEnum::LoyaltyLevelId->dbName()      => fn ($value) => $this->cellStyleCenter($value),
            ClientModelEnum::CustomPlayerCategory->dbName()     => fn ($value) => $this->cellStyleCenter($value),
            ClientModelEnum::DepositCount->dbName()             => fn ($value) => $this->cellStyleCenter(number_format($value)),
            ClientModelEnum::IsTest->dbName()                   => fn ($value) => $this->cellStyleCenter($value ? 'YES' : 'NO'),
            ClientModelEnum::ProvinceInternal->dbName()         => fn ($value) => $this->cellStyleCenter($this->translateProvinceName($value)),
            ClientModelEnum::CityInternal->dbName()             => fn ($value, $attributes) => $this->cellStyleCenter($this->translateCityName($attributes[ClientModelEnum::ProvinceInternal->dbName()], $value)),
            ClientModelEnum::JobFieldInternal->dbName()         => fn ($value) => $this->cellStyleCenter($this->translateJobFieldName($value)),

            ClientModelEnum::ContactNumbersInternal->dbName()   => fn ($value) => $this->cellStyleWrapText($this->cellStyleCenter($this->convertJsonList($value))),
            ClientModelEnum::ContactMethodsInternal->dbName()   => fn ($value) => $this->cellStyleWrapText($this->cellStyleCenter($this->convertEnumList($value, ContactMethodsEnum::class))),
            ClientModelEnum::CallerGenderInternal->dbName()     => fn ($value) => $this->cellStyleWrapText($this->cellStyleCenter($this->convertEnumList($value, ExternalAdminGendersEnum::class))),

            UsersTableEnum::IsEmailVerified->dbName()           => fn () => $this->cellStyleCenter(ClientProfileCheckEnum::LastEmailVerification->isCompleted($recordUser) ? 'YES' : 'NO'),
            'IsProfileFurtherInfoCompleted'                     => fn () => $this->cellStyleCenter(ClientProfileCheckEnum::FurtherInformationTab->isCompleted($recordUser) ? 'YES' : 'NO'),
            ClientModelEnum::Descr->dbName()                    => fn ($value) => $this->cellStyleCenter($value),
        ];
    }

    /**
     * Convert json list to dispaly text list
     *
     * @param  string|array|null $list
     * @param  ?string $separator
     * @return string
     */
    private function convertJsonList(string|array|null $list, ?string $separator = "\n"): string
    {
        if (empty($list))
            return "";

        if (!is_array($list)) {

            if (!JsonHelper::isJson($list))
                return $list;

            $listArray = json_decode($list);
        } else
            $listArray = $list;

        return implode($separator, $listArray);
    }

    /**
     * Convert enum list taht stored as json string
     *
     * @param  ?string $jsonList
     * @param string $enumClass
     * @param  ?string $separator
     * @return string
     */
    private function convertEnumList(?string $jsonList, string $enumClass, ?string $separator = "\n"): string
    {
        if (empty($jsonList))
            return "";

        if (!JsonHelper::isJson($jsonList))
            return $jsonList;

        $listArray = json_decode($jsonList);

        $translatedArray = [];
        foreach ($listArray as $item) {

            if ($itemCase = $enumClass::getCase($item)) {
                array_push($translatedArray, $itemCase->translate());
            }
        }

        return implode($separator, $translatedArray);
    }

    /**
     * Translate name
     *
     * @param  ?string $langKey
     * @return string
     */
    private function translateName(?string $langKey): string
    {
        $translatedName = __($langKey);
        if ($translatedName == $langKey)
            $translatedName = ""; // Not found

        return $translatedName;
    }

    /**
     * Translate provinces name
     *
     * @param  ?string $province
     * @return string
     */
    private function translateProvinceName(?string $province): string
    {
        return $this->translateName(sprintf('IranCities.Provinces.%s', $province));
    }

    /**
     * Translate city name
     *
     * @param  ?string $province
     * @param  ?string $city
     * @return string
     */
    private function translateCityName(?string $province, ?string $city): string
    {
        return $this->translateName(sprintf('IranCities.Cities.%s.%s', $province, $city));
    }

    /**
     * Translate provinces name
     *
     * @param  ?string $jobField
     * @return string
     */
    private function translateJobFieldName(?string $jobField): string
    {
        return $this->translateName(sprintf('thisApp.JobFields.%s', $jobField));
    }
}
