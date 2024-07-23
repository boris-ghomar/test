<?php

namespace App\Http\Controllers\BackOffice\ClientsManagement;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Routes\AdminRoutesEnum;
use App\Enums\Users\UsersStatusEnum;
use App\hhh_Exports\ClientsManagement\UserBetconstructExport;
use App\HHH_Library\general\php\ArrayHelper;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\GendersEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ClientSwarmModelEnum;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\ClientsManagement\UserBetconstructRequest;
use App\Http\Resources\BackOffice\ClientsManagement\UserBetconstructCollection;
use App\Http\Resources\BackOffice\ClientsManagement\UserBetconstructResource;
use App\Interfaces\CustomizableJsGridPage;
use App\Interfaces\ExcelExportableJsGridPage;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use App\Models\User;
use App\Policies\BackOffice\Global\GlobalViewClientEmailPolicy;
use App\Policies\BackOffice\Global\GlobalViewClientPhonePolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserBetconstructController extends SuperJsGridController implements CustomizableJsGridPage, ExcelExportableJsGridPage
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, UserBetconstruct::class);

        $customizablePageSettings = $this->getCustomizablePageSettings();
        $selectedColumns = $customizablePageSettings[config('hhh_config.keywords.selectedColumns')];


        $jsGrid_Controller = parent::getJsGridType(UserBetconstruct::class, jsGrid_Controller::jsGridType_EditDelete);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("clients_management/betconstruct_clients");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'login'])));

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(UsersTableEnum::Id->dbName(), __('thisApp.UserId'));
        $fieldMaker->makeField_Number();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "130");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldName = UsersTableEnum::RoleId->dbName();
        if (in_array($fieldName, $selectedColumns)) {

            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("general.Category"));
            $options = DropdownListCreater::makeByModel(ClientCategory::class, RolesTableEnum::Name->dbName())
                ->prepend("", -1)->useLable("name", "id")->sort(true)->get();
            $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
            $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value > 0;}", trans('validation.required', ['attribute' => __('general.Category')]));
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = UsersTableEnum::Status->dbName();
        if (in_array($fieldName, $selectedColumns)) {

            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("general.AccountStatus"));
            $options = DropdownListCreater::makeByArray(UsersStatusEnum::translatedArray())
                ->prepend("", "")->useLable("name", "key")->sort(true)->get();
            $selectedIndex = ArrayHelper::searchMultiDimentional(UsersStatusEnum::Active->name, 'key', $options);
            $fieldMaker->makeField_Select('key', 'name', $selectedIndex, 'string', $options);
            $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value != '';}", trans('validation.required', ['attribute' => __('general.AccountStatus')]));
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::Login->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("general.UserName"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = 'betconstruct_id';
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __('thisApp.BetconstructId'));
            $fieldMaker->makeField_Number();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::Email->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("general.Email"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::FirstName->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("general.FristName"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::LastName->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("general.LastName"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::Gender->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("general.Gender"));
            $options = DropdownListCreater::makeByArray(GendersEnum::translatedArray())
                ->prepend("", -1)
                ->useLable("name", "id")
                ->get();
            $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::Phone->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("general.Phone"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::MobilePhone->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("general.Mobile"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::BirthDateStamp->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __('general.Birthday'));
            $fieldMaker->makeField_DateRange();
            $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::RegionCode->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.RegionCode"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::CurrencyId->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.CurrencyId"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::Balance->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.Balance"));
            $fieldMaker->makeField_NumberRange();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::UnplayedBalance->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.UnplayedBalance"));
            $fieldMaker->makeField_NumberRange();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::LastLoginIp->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.LastLoginIp"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::LastLoginTimeStamp->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.LastLoginTimeStamp"));
            $fieldMaker->makeField_DateRange();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::City->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, sprintf('%s (%s)', __("bc_api.City"), __("thisApp.Betconstruct")));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::CreatedStamp->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.CreatedStamp"));
            $fieldMaker->makeField_DateRange();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::ModifiedStamp->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.ModifiedStamp"));
            $fieldMaker->makeField_DateRange();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientSwarmModelEnum::LoyaltyLevelId->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.LoyaltyLevelId"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::CustomPlayerCategory->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.CustomPlayerCategory"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::DepositCount->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.DepositCount"));
            $fieldMaker->makeField_NumberRange();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::IsTest->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("bc_api.IsTest"));
            $fieldMaker->makeField_Checkbox();
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::ProvinceInternal->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("general.province"));
            $options = DropdownListCreater::makeByArray(__('IranCities.Provinces'))
                ->sort(true)->useReverseList()->prepend("", "")->useLable("name", "id")->get();
            $fieldMaker->makeField_Select('id', 'name', "", 'string', $options);
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::CityInternal->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("general.city"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::JobFieldInternal->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("thisApp.JobField"));
            $options = DropdownListCreater::makeByArray(__('thisApp.JobFields'))
                ->sort(true)->useReverseList()->prepend("", "")->useLable("name", "id")->get();
            $fieldMaker->makeField_Select('id', 'name', "", 'string', $options);
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::ContactNumbersInternal->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("thisApp.ContactNumbers"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
            $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::ContactMethodsInternal->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("thisApp.ContactMethods"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::CallerGenderInternal->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("thisApp.CallerGender"));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = UsersTableEnum::IsEmailVerified->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("thisApp.AdminPages.ClientsManagement.IsEmailVerified"));
            $fieldMaker->makeField_Checkbox();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = 'IsProfileFurtherInfoCompleted';
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __("thisApp.AdminPages.ClientsManagement.IsProfileFurtherInfoCompleted"));
            $fieldMaker->makeField_Checkbox();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
            $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldName = ClientModelEnum::Descr->dbName();
        if (in_array($fieldName, $selectedColumns)) {
            $fieldMaker = new jsGrid_FieldMaker($fieldName, __('general.Description'));
            $fieldMaker->makeField_Textarea();
            $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
            $jsGrid_Controller->putField($fieldMaker);
        }

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')            =>  $jsGrid_Controller->create(),
            config('hhh_config.keywords.customizablePage')          =>  true,
            config('hhh_config.keywords.customizablePageSettings')  =>  $this->getCustomizablePageSettings(),
            config('hhh_config.keywords.useExcelExport')            =>  User::authUser()->can(PermissionAbilityEnum::export->name, UserBetconstruct::class),
        ];

        return view('hhh.BackOffice.pages.ClientsManagement.BetconstructClients.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserBetconstruct $userBetconstruct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserBetconstruct $userBetconstruct)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\ClientsManagement\UserBetconstructRequest $request
     * @param  \App\Models\BackOffice\ClientsManagement\UserBetconstruct $userBetconstruct
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(UserBetconstructRequest $request, UserBetconstruct $userBetconstruct): JsonResponse
    {
        try {

            // Save "users" table data
            $id = $request->input(UsersTableEnum::Id->dbName());
            $userBetconstruct = UserBetconstruct::find($id);
            $userBetconstruct->fill($request->only([
                UsersTableEnum::RoleId->dbName(),
                UsersTableEnum::Status->dbName(),
            ]));

            $userBetconstruct->save();

            // Save "betconstruct_clients" table data
            $updatableCases = [
                ClientModelEnum::Email,
                ClientModelEnum::FirstName,
                ClientModelEnum::LastName,
                ClientModelEnum::Gender,
                ClientModelEnum::Phone,
                ClientModelEnum::MobilePhone,
                ClientModelEnum::BirthDateStamp,
                ClientModelEnum::IsTest,
            ];
            $updatableData = [];
            foreach ($updatableCases as $case) {

                $caseDbName = $case->dbName();
                if ($request->has($caseDbName))
                    $updatableData[$case->name] = $request->input($caseDbName);
            }

            $descrCol = ClientModelEnum::Descr->dbName();

            $betconstructClient = $userBetconstruct->betconstructClient;
            $betconstructClient = ClientModelEnum::fillModel($updatableData, $betconstructClient);
            $betconstructClient[$descrCol] = $request->input($descrCol, $betconstructClient->$descrCol);

            $updateResult = ClientModelEnum::updateBetconstructClientData($betconstructClient);

            if (is_string($updateResult)) {
                // Betconstruct error
                return JsonResponseHelper::errorResponse(null, $updateResult, HttpResponseStatusCode::BadRequest->value);
            }
        } catch (\Throwable $th) {

            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        $resource = UserBetconstruct::ApiIndexCollection([UsersTableEnum::Id->dbName() => $id])->first();
        return JsonResponseHelper::successResponse(new UserBetconstructResource($resource), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\PeronnelManagement\PersonnelRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(UserBetconstructRequest $request): JsonResponse
    {
        if ($userBetconstruct = UserBetconstruct::find($request->input(UsersTableEnum::Id->dbName()))) {

            // Delete user settings
            $userBetconstruct->userSettings()->delete();

            $userBetconstruct->delete();

            /* Do not delete user extra, as the user has soft delete and maybe recovered
            $userBetconstruct->betconstructClient->delete(); */

            return JsonResponseHelper::successResponse(null, trans('general.ItemSuccessfullyRemoved'));
        }

        return JsonResponseHelper::errorResponse(null, trans('general.NotFoundItem'), HttpResponseStatusCode::BadRequest->value);
    }

    /**
     * Return the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiIndex(Request $request)
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, UserBetconstruct::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new UserBetconstructCollection(
            UserBetconstruct::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }

    /**
     * Define page route
     *
     * @return object : \App\Enums\Routes\AdminRoutesEnum|\App\Enums\Routes\AdminPublicRoutesEnum|\App\Enums\Routes\SiteRoutesEnum|\App\Enums\Routes\SitePublicRoutesEnum
     */
    public static function customizablePageRoute(): object
    {
        return AdminRoutesEnum::BetconstructClients_Management;
    }

    /**
     * Get customizable page required columns
     *
     * @return array
     */
    public static function getCustomizablePageRequiredColumns(): array
    {
        return [
            UsersTableEnum::Id->dbName() => __("thisApp.UserId"),
        ];
    }

    /**
     * Get customizable page selectable columns
     *
     * @return array
     */
    public static function getCustomizablePageSelectableColumns(): array
    {
        // Put by order for export
        $selectableColumns =  [
            UsersTableEnum::RoleId->dbName() => __("general.Category"),
            UsersTableEnum::Status->dbName() => __("general.AccountStatus"),
            ClientModelEnum::Login->dbName() => __("general.UserName"),
            'betconstruct_id' => __("thisApp.BetconstructId"),
            ClientModelEnum::Email->dbName() => __("general.Email"),
            ClientModelEnum::FirstName->dbName() => __("general.FristName"),
            ClientModelEnum::LastName->dbName() => __("general.LastName"),
            ClientModelEnum::Gender->dbName() => __("general.Gender"),
            ClientModelEnum::Phone->dbName() => __("general.Phone"),
            ClientModelEnum::MobilePhone->dbName() => __("general.Mobile"),
            ClientModelEnum::BirthDateStamp->dbName() => __("general.Birthday"),
            ClientModelEnum::RegionCode->dbName() => __("bc_api.RegionCode"),
            ClientModelEnum::CurrencyId->dbName() => __("bc_api.CurrencyId"),
            ClientModelEnum::Balance->dbName() => __("bc_api.Balance"),
            ClientModelEnum::UnplayedBalance->dbName() => __("bc_api.UnplayedBalance"),
            ClientModelEnum::LastLoginIp->dbName() => __("bc_api.LastLoginIp"),
            ClientModelEnum::LastLoginTimeStamp->dbName() => __("bc_api.LastLoginTimeStamp"),
            ClientModelEnum::City->dbName() => sprintf('%s (%s)', __("bc_api.City"), __("thisApp.Betconstruct")),
            ClientModelEnum::CreatedStamp->dbName() => __("bc_api.CreatedStamp"),
            ClientModelEnum::ModifiedStamp->dbName() => __("bc_api.ModifiedStamp"),
            ClientSwarmModelEnum::LoyaltyLevelId->dbName() => __("bc_api.LoyaltyLevelId"),
            ClientModelEnum::CustomPlayerCategory->dbName() => __("bc_api.CustomPlayerCategory"),
            ClientModelEnum::DepositCount->dbName() => __("bc_api.DepositCount"),
            ClientModelEnum::IsTest->dbName() => __("bc_api.IsTest"),
            ClientModelEnum::ProvinceInternal->dbName() => __("general.province"),
            ClientModelEnum::CityInternal->dbName() => __("general.city"),
            ClientModelEnum::JobFieldInternal->dbName() => __("thisApp.JobField"),
            ClientModelEnum::ContactNumbersInternal->dbName() => __("thisApp.ContactNumbers"),
            ClientModelEnum::ContactMethodsInternal->dbName() => __("thisApp.ContactMethods"),
            ClientModelEnum::CallerGenderInternal->dbName() => __("thisApp.CallerGender"),
            UsersTableEnum::IsEmailVerified->dbName() => __("thisApp.AdminPages.ClientsManagement.IsEmailVerified"),
            "IsProfileFurtherInfoCompleted" => __("thisApp.AdminPages.ClientsManagement.IsProfileFurtherInfoCompleted"),
            ClientModelEnum::Descr->dbName() => __("general.Description"),
        ];

        $user = User::authUser();

        if ($user->cannot(PermissionAbilityEnum::view->name, GlobalViewClientEmailPolicy::class))
            unset($selectableColumns[ClientModelEnum::Email->dbName()]);

        if ($user->cannot(PermissionAbilityEnum::view->name, GlobalViewClientPhonePolicy::class)) {
            unset($selectableColumns[ClientModelEnum::Phone->dbName()]);
            unset($selectableColumns[ClientModelEnum::MobilePhone->dbName()]);
            unset($selectableColumns[ClientModelEnum::ContactNumbersInternal->dbName()]);
        }

        return $selectableColumns;
    }

    /**
     * Get customizable page settings
     *
     * @return array
     */
    public function getCustomizablePageSettings(): array
    {
        $user = User::authUser();

        $customizablePageSettings = parent::getCustomizablePageSettings();

        $selectedColumnsKey = config('hhh_config.keywords.selectedColumns');
        $requiredColumnsKey = config('hhh_config.keywords.requiredColumns');
        $displayColumnsKey = config('hhh_config.keywords.displayColumns');

        $selectedColumns = $customizablePageSettings[$selectedColumnsKey];

        if ($user->cannot(PermissionAbilityEnum::view->name, GlobalViewClientEmailPolicy::class)) {

            $selectedColumns = ArrayHelper::removeItems($selectedColumns, [ClientModelEnum::Email->dbName()]);
        }

        if ($user->cannot(PermissionAbilityEnum::view->name, GlobalViewClientPhonePolicy::class)) {

            $selectedColumns = ArrayHelper::removeItems($selectedColumns, [
                ClientModelEnum::Phone->dbName(),
                ClientModelEnum::MobilePhone->dbName(),
                ClientModelEnum::ContactNumbersInternal->dbName(),
            ]);
        }

        $customizablePageSettings[$selectedColumnsKey] = $selectedColumns;
        $customizablePageSettings[$displayColumnsKey] = array_merge(array_keys($customizablePageSettings[$requiredColumnsKey]), $selectedColumns);

        return $customizablePageSettings;
    }

    /**
     * This function exports the table information
     * to an Excel file and downloads it to the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportExcel(Request $request): JsonResponse
    {
        $this->authorize(PermissionAbilityEnum::export->name, UserBetconstruct::class);

        $exporter = new UserBetconstructExport($request->all());
        return $exporter->export();
    }
}
