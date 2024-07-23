<?php

namespace App\Http\Resources\BackOffice\ClientsManagement;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Users\ClientProfileCheckEnum;
use App\Enums\Users\ContactMethodsEnum;
use App\HHH_Library\general\php\JsonHelper;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\GendersEnum as ExternalAdminGendersEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ClientSwarmModelEnum;
use App\Http\Controllers\BackOffice\ClientsManagement\UserBetconstructController;;
use App\Http\Resources\ApiResponseResource;
use App\Models\User;

class UserBetconstructResource extends ApiResponseResource
{

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
            UsersTableEnum::Id->dbName()                => fn ($value) => (int) $value,
            UsersTableEnum::RoleId->dbName()            => fn ($value) => (int) $value,
            UsersTableEnum::Status->dbName()            => fn ($value) => $value,

            'betconstruct_id'                                   => fn ($value) => (int) $value,
            ClientModelEnum::Login->dbName()                    => fn ($value) => $value,
            ClientModelEnum::Email->dbName()                    => fn ($value) => $value,
            ClientModelEnum::FirstName->dbName()                => fn ($value) => $value,
            ClientModelEnum::LastName->dbName()                 => fn ($value) => $value,
            ClientModelEnum::Gender->dbName()                   => fn ($value) => $value,
            ClientModelEnum::Phone->dbName()                    => fn ($value) => $value,
            ClientModelEnum::MobilePhone->dbName()              => fn ($value) => $value,
            ClientModelEnum::BirthDateStamp->dbName()           => fn ($value) => $authUser->convertUTCToLocalTime($value, true),
            ClientModelEnum::RegionCode->dbName()               => fn ($value) => $value,
            ClientModelEnum::CurrencyId->dbName()               => fn ($value) => $value,
            ClientModelEnum::Balance->dbName()                  => fn ($value) => number_format($value, 2),
            ClientModelEnum::UnplayedBalance->dbName()          => fn ($value) => number_format($value, 2),
            ClientModelEnum::LastLoginIp->dbName()              => fn ($value) => $value,
            ClientModelEnum::LastLoginTimeStamp->dbName()       => fn ($value) => $authUser->convertUTCToLocalTime($value, true),
            ClientModelEnum::City->dbName()                     => fn ($value) => $value,
            ClientModelEnum::CreatedStamp->dbName()             => fn ($value) => $authUser->convertUTCToLocalTime($value, true),
            ClientModelEnum::ModifiedStamp->dbName()            => fn ($value) => $authUser->convertUTCToLocalTime($value, true),
            ClientModelEnum::CustomPlayerCategory->dbName()     => fn ($value) => $value,
            ClientModelEnum::DepositCount->dbName()             => fn ($value) => number_format($value),
            ClientModelEnum::IsTest->dbName()                   => fn ($value) => (bool) $value,
            ClientModelEnum::ProvinceInternal->dbName()         => fn ($value) => $value,
            ClientModelEnum::CityInternal->dbName()             => fn ($value, $attributes) => $this->translateCityName($attributes[ClientModelEnum::ProvinceInternal->dbName()], $value),
            ClientModelEnum::JobFieldInternal->dbName()         => fn ($value) => $value,
            ClientModelEnum::ContactNumbersInternal->dbName()   => fn ($value) => $this->convertJsonList($value),
            ClientModelEnum::ContactMethodsInternal->dbName()   => fn ($value) => $this->convertEnumList($value, ContactMethodsEnum::class),
            ClientModelEnum::CallerGenderInternal->dbName()     => fn ($value) => $this->convertEnumList($value, ExternalAdminGendersEnum::class),
            UsersTableEnum::IsEmailVerified->dbName()           => fn () => (bool) ClientProfileCheckEnum::LastEmailVerification->isCompleted($recordUser),
            'IsProfileFurtherInfoCompleted'                     => fn () => (bool) ClientProfileCheckEnum::FurtherInformationTab->isCompleted($recordUser),

            ClientSwarmModelEnum::LoyaltyLevelId->dbName()      => fn ($value) => $value,


            ClientModelEnum::Descr->dbName()                    => fn ($value) => $value,
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
     * Translate city name
     *
     * @param  ?string $province
     * @param  ?string $city
     * @return string
     */
    private function translateCityName(?string $province, ?string $city): string
    {
        $cityInternalLangKey = sprintf('IranCities.Cities.%s.%s', $province, $city);
        $translatedName = __($cityInternalLangKey);
        if ($translatedName == $cityInternalLangKey)
            $translatedName = ""; // City name not found

        return $translatedName;
    }
}
