<?php

namespace App\Http\Controllers\BackOffice\PeronnelManagement;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\PermissionRoleTableEnum;
use App\Enums\Database\Tables\PermissionsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Routes\AdminRoutesEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\PeronnelManagement\PermissionRoleRequest;
use App\Http\Resources\BackOffice\PeronnelManagement\PersonnelPermissionRoleCollection;
use App\Http\Resources\BackOffice\PeronnelManagement\PersonnelPermissionRoleResource;
use App\Models\BackOffice\AccessControl\Permission;
use App\Models\BackOffice\AccessControl\PermissionAdminPanel;
use App\Models\BackOffice\PeronnelManagement\PersonnelPermissionRole;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonnelPermissionRoleController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, PersonnelPermissionRole::class);

        $this->updatePivotTable();

        $jsGrid_Controller = parent::getJsGridType(PersonnelPermissionRole::class, jsGrid_Controller::jsGridType_Edit);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("personnel_management/role-permissions");
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

        $fieldMaker = new jsGrid_FieldMaker(RolesTableEnum::Name->dbName(), __("general.Role"));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PermissionsTableEnum::Route->dbName(), __("thisApp.AdminPages.AccessControl.Permissions.Route"));
        $options = DropdownListCreater::makeByArray(AdminRoutesEnum::translatedArray())
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
            ->onlyUsedValuesInModel(PermissionAdminPanel::class, PermissionsTableEnum::Ability->dbName())
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


        return view('hhh.BackOffice.pages.PersonnelManagement.PersonnelPermissionRole.index', $data);
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
    public function show(PersonnelPermissionRole $personnelPermissionRole)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PersonnelPermissionRole $personnelPermissionRole)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\PeronnelManagement\PermissionRoleRequest $request
     * @param  \App\Models\BackOffice\AccessControl\PermissionRole $permissionRole
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(PermissionRoleRequest $request, PersonnelPermissionRole $personnelPermissionRole): JsonResponse
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

            $personnelPermissionRole = PersonnelPermissionRole::where(PermissionRoleTableEnum::PermissionId->dbName(), $request->input(PermissionRoleTableEnum::PermissionId->dbName()))
                ->where(PermissionRoleTableEnum::RoleId->dbName(), $request->input(PermissionRoleTableEnum::RoleId->dbName()))
                ->first();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new PersonnelPermissionRoleResource($personnelPermissionRole), null, HttpResponseStatusCode::Accepted->value);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PersonnelPermissionRole $personnelPermissionRole)
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, PersonnelPermissionRole::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new PersonnelPermissionRoleCollection(
            PersonnelPermissionRole::ApiIndexCollection($request->input())
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

        $adminPanelPermissions = Permission::AdminPanelPermissions()->get();
        $adminPanelRolesIds = PersonnelRole::pluck('id')->all();

        foreach ($adminPanelPermissions as $permission) {

            $permission->roles()->sync($adminPanelRolesIds);
        }
    }
}
