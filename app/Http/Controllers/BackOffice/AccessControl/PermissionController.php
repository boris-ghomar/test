<?php

namespace App\Http\Controllers\BackOffice\AccessControl;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\AccessControl\PermissionTypeEnum;
use App\Enums\Routes\AdminRoutesEnum;
use App\Enums\Database\Tables\PermissionsTableEnum as TableEnum;
use App\Enums\Routes\SiteRoutesEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\AccessControl\PermissionRequest;
use App\Http\Requests\traits\authorizeMethods;
use App\Http\Resources\BackOffice\AccessControl\PermissionCollection;
use App\Http\Resources\BackOffice\AccessControl\PermissionResource;
use App\Models\BackOffice\AccessControl\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends SuperJsGridController
{
    use authorizeMethods;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, Permission::class);

        $this->updatePermissionsTable();

        $jsGrid_Controller = parent::getJsGridType(Permission::class, jsGrid_Controller::jsGridType_Edit);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("access_control/permissions");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Route->dbName(), __("thisApp.AdminPages.AccessControl.Permissions.Route"));
        $routeItems = array_merge(AdminRoutesEnum::translatedArray(), SiteRoutesEnum::translatedArray());
        $options = DropdownListCreater::makeByArray($routeItems)
            ->onlyUsedValuesInModel(Permission::class, TableEnum::Route->dbName())
            ->useLable("name", "key")->prepend("", "")->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, 300);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "start");
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Ability->dbName(), __("thisApp.AdminPages.AccessControl.Permissions.Ability"));
        $options = DropdownListCreater::makeByArray(PermissionAbilityEnum::translatedArray())
            ->onlyUsedValuesInModel(Permission::class, TableEnum::Ability->dbName())
            ->useLable("name", "key")->prepend("", "")->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Type->dbName(), __("thisApp.AdminPages.AccessControl.Permissions.Type"));
        $options = DropdownListCreater::makeByArray(PermissionTypeEnum::translatedArray())
            ->onlyUsedValuesInModel(Permission::class, TableEnum::Type->dbName())
            ->useLable("name", "key")->prepend("", "")->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsActive->dbName(), __('general.isActive'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Descr->dbName(), __('general.Description'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.AccessControl.permissions.index', $data);
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
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\AccessControl\PermissionRequest $request
     * @param  \App\Models\BackOffice\AccessControl\Permission $permission
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(PermissionRequest $request, Permission $permission): JsonResponse
    {
        try {
            $permission = Permission::find($request->input(TableEnum::Id->dbName()));
            $permission->fill($request->all());
            $permission->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(),  HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new PermissionResource($permission), null, HttpResponseStatusCode::Accepted->value);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
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
        $this->authorize(PermissionAbilityEnum::viewAny->name, Permission::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new PermissionCollection(
            Permission::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }


    /**
     * This function updates the permissions table based on the
     * settings of the enum files.
     *
     * Deleted routes will be removed from the table and
     * new routes will be added to the database and changes will be updated.
     *
     * In each call, the previously registered permissions
     * that have not changed remain unchanged,
     * so the user will not have any problems regarding previously permissions.
     *
     * Conclusion:
     * To change the permissions, just update the enum files
     * and run this page once to update the database.
     *
     * @return void
     */
    private function updatePermissionsTable(): void
    {

        $this->clearDeletedPermissions();
        $this->updatePermissions();
    }


    /**
     * Update permissions table in database
     *
     * @return void
     */
    private function updatePermissions(): void
    {

        foreach (PermissionTypeEnum::cases() as $permissionTypeCase) {

            foreach ((array) $permissionTypeCase->getRoutesCases() as $routeCase) {

                $abilities = $routeCase->abilities();

                foreach (PermissionAbilityEnum::names() as $ability) {

                    if (in_array($ability, $abilities)) {

                        $permission = Permission::where(TableEnum::Route->dbName(), $routeCase->value)
                            ->where(TableEnum::Ability->dbName(), $ability)
                            ->first();

                        if (is_null($permission)) {
                            $permission = new Permission();
                            $permission[TableEnum::IsActive->dbName()] = true; // New permission default
                        }

                        $permission->fill([
                            TableEnum::Route->dbName()       => $routeCase->value,
                            TableEnum::Ability->dbName()     => $ability,
                            TableEnum::Type->dbName()        => $permissionTypeCase->name,
                        ]);

                        $permission->save();
                    }
                }
            }
        }
    }

    /**
     * Clear permissions table from deleted permissions in Enums
     *
     * @return void
     */
    private function clearDeletedPermissions(): void
    {

        foreach (Permission::all() as $permission) {

            $permissionType = $permission[TableEnum::Type->dbName()];

            // Check permission->type exists or not
            if (!PermissionTypeEnum::hasName($permissionType)) {

                $permission->roles()->detach();
                $permission->delete();
            } else {

                // Check permission->route exists or not
                $routesClass = PermissionTypeEnum::getCase($permissionType)->getRoutesClass();
                $permissionRoute = $permission[TableEnum::Route->dbName()];

                if (!$routesClass::hasValue($permissionRoute)) {

                    $permission->roles()->detach();
                    $permission->delete();
                } else {

                    // Check permission->ability exists or not
                    $routeCaseName =  $routesClass::getNameByValue($permissionRoute);
                    $routeAbilities = constant($routesClass . '::' . $routeCaseName)->abilities();
                    $permissionAbility = $permission[TableEnum::Ability->dbName()];

                    if (!in_array($permissionAbility, $routeAbilities)) {

                        $permission->roles()->detach();
                        $permission->delete();
                    }
                }
            }
        }
    }
}
