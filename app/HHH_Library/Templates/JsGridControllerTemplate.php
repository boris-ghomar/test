<?php

namespace App\HHH_Library\Templates;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\RolesTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\PeronnelManagement\PersonnelRoleRequest;
use App\Http\Resources\BackOffice\PeronnelManagement\PersonnelRoleCollection;
use App\Http\Resources\BackOffice\PeronnelManagement\PersonnelRoleResource;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JsGridControllerTemplate extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, PersonnelRole::class);

        $jsGrid_Controller = parent::getJsGridType(PersonnelRole::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("personnel_management/roles");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new PersonnelRoleRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Name->dbName(), __('general.Name'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Name->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
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


        return view('hhh.BackOffice.pages.PersonnelManagement.PersonnelRoles.index', $data);
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
     *
     * @param  \App\Http\Requests\BackOffice\PeronnelManagement\PersonnelRoleRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(PersonnelRoleRequest $request): JsonResponse
    {
        try {

            $item = new PersonnelRole();

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new PersonnelRoleResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\PeronnelManagement\PersonnelRole $personnelRole
     */
    public function show(PersonnelRole $personnelRole)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\PeronnelManagement\PersonnelRole $personnelRole
     */
    public function edit(PersonnelRole $personnelRole)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\PeronnelManagement\PersonnelRoleRequest $request
     * @param \App\Models\BackOffice\PeronnelManagement\PersonnelRole $personnelRole
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(PersonnelRoleRequest $request, PersonnelRole $personnelRole): JsonResponse
    {
        try {

            $item = PersonnelRole::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new PersonnelRoleResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\PeronnelManagement\PersonnelRoleRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(PersonnelRoleRequest $request): JsonResponse
    {
        if ($item = PersonnelRole::find($request->input(TableEnum::Id->dbName()))) {

            $item->permissions()->detach();

            $item->delete();
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, PersonnelRole::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new PersonnelRoleCollection(
            PersonnelRole::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
