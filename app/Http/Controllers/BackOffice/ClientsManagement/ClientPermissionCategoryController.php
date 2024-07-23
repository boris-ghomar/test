<?php

namespace App\Http\Controllers\BackOffice\ClientsManagement;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\PermissionRoleTableEnum;
use App\Enums\Database\Tables\PermissionsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Routes\SiteRoutesEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\ClientsManagement\ClientPermissionCategoryRequest;
use App\Http\Resources\BackOffice\ClientsManagement\ClientPermissionCategoryCollection;
use App\Http\Resources\BackOffice\ClientsManagement\ClientPermissionCategoryResource;
use App\Models\BackOffice\AccessControl\Permission;
use App\Models\BackOffice\AccessControl\PermissionSite;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\BackOffice\ClientsManagement\ClientPermissionCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientPermissionCategoryController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, ClientPermissionCategory::class);

        $this->updatePivotTable();

        $jsGrid_Controller = parent::getJsGridType(ClientPermissionCategory::class, jsGrid_Controller::jsGridType_Edit);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("clients_management/category-permissions");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(PermissionRoleTableEnum::PermissionId->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PermissionRoleTableEnum::RoleId->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(RolesTableEnum::Name->dbName(), __("general.Category"));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PermissionsTableEnum::Route->dbName(), __("thisApp.AdminPages.AccessControl.Permissions.Route"));
        $options = DropdownListCreater::makeByArray(SiteRoutesEnum::translatedArray())
            ->onlyUsedValuesInModel(Permission::class, PermissionsTableEnum::Route->dbName())
            ->notAllowedValues(Permission::NotActive()->pluck(PermissionsTableEnum::Route->dbName())->toArray())
            ->useLable("name", "key")->prepend("", "")->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, 300);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "start");
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PermissionsTableEnum::Ability->dbName(), __("thisApp.AdminPages.AccessControl.Permissions.Ability"));
        $options = DropdownListCreater::makeByArray(PermissionAbilityEnum::translatedArray())
            ->onlyUsedValuesInModel(PermissionSite::class, PermissionsTableEnum::Ability->dbName())
            ->useLable("name", "key")->prepend("", "")->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PermissionsTableEnum::IsActive->dbName(), __('general.isActive'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PermissionsTableEnum::Descr->dbName(), __('general.Description'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.ClientsManagement.ClientPermissionCategory.index', $data);
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
    public function show(ClientPermissionCategory $clientPermissionCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClientPermissionCategory $clientPermissionCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\ClientsManagement\ClientPermissionCategoryRequest $request
     * @param  \App\Models\BackOffice\ClientsManagement\ClientPermissionCategory $clientPermissionCategory
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(ClientPermissionCategoryRequest $request, ClientPermissionCategory $clientPermissionCategory): JsonResponse
    {
        try {

            $permission = Permission::find($request->input(PermissionRoleTableEnum::PermissionId->dbName()));
            $permission->roles()->updateExistingPivot(
                $request->input(PermissionRoleTableEnum::RoleId->dbName()),
                [
                    PermissionRoleTableEnum::IsActive->dbName() => $request->input(PermissionRoleTableEnum::IsActive->dbName()),
                    PermissionRoleTableEnum::Descr->dbName() => $request->input(PermissionRoleTableEnum::Descr->dbName()),
                ]
            );

            $clientPermissionCategory = ClientPermissionCategory::where(PermissionRoleTableEnum::PermissionId->dbName(), $request->input(PermissionRoleTableEnum::PermissionId->dbName()))
                ->where(PermissionRoleTableEnum::RoleId->dbName(), $request->input(PermissionRoleTableEnum::RoleId->dbName()))
                ->first();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ClientPermissionCategoryResource($clientPermissionCategory), null, HttpResponseStatusCode::Accepted->value);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClientPermissionCategory $clientPermissionCategory)
    {
        //
    }

    /**
     * Return the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiIndex(Request $request)
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, ClientPermissionCategory::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ClientPermissionCategoryCollection(
            ClientPermissionCategory::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }

    /**
     * Sync permissions with admin panel roles
     *
     * @return void
     */
    public function updatePivotTable()
    {

        $sitePermissions = Permission::SitePermissions()->get();
        $clientCategoriesIds = ClientCategory::pluck('id')->all();

        foreach ($sitePermissions as $permission) {

            $permission->roles()->sync($clientCategoriesIds);
        }
    }
}
