<?php

namespace App\Http\Controllers\BackOffice\ClientsManagement;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\RolesTableEnum as TableEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\ClientsManagement\ClientCategoryRequest;
use App\Http\Resources\BackOffice\ClientsManagement\ClientCategoryCollection;
use App\Http\Resources\BackOffice\ClientsManagement\ClientCategoryResource;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientCategoryController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, ClientCategory::class);

        $jsGrid_Controller = parent::getJsGridType(ClientCategory::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("clients_management/categories");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new ClientCategoryRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Name->dbName(), __('general.Name'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Name->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::DisplayName->dbName(), __('thisApp.DisplayName'));
        $fieldMaker->makeField_Text();
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


        return view('hhh.BackOffice.pages.ClientsManagement.ClientCategories.index', $data);
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
     * @param  App\Http\Requests\BackOffice\ClientsManagement\ClientCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(ClientCategoryRequest $request): JsonResponse
    {
        try {

            $item = new ClientCategory();

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ClientCategoryResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     * @param \App\Models\BackOffice\ClientsManagement\ClientCategory $clientCategory
     */
    public function show(ClientCategory $clientCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param \App\Models\BackOffice\ClientsManagement\ClientCategory $clientCategory
     */
    public function edit(ClientCategory $clientCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\ClientsManagement\ClientCategoryRequest $request
     * @param \App\Models\BackOffice\ClientsManagement\ClientCategory $clientCategory
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(ClientCategoryRequest $request, ClientCategory $clientCategory): JsonResponse
    {
        try {

            $item = ClientCategory::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ClientCategoryResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Http\Requests\BackOffice\ClientsManagement\ClientCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ClientCategoryRequest $request): JsonResponse
    {
        if ($item = ClientCategory::find($request->input(TableEnum::Id->dbName()))) {

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

        $this->authorize(PermissionAbilityEnum::viewAny->name, ClientCategory::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ClientCategoryCollection(
            ClientCategory::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
