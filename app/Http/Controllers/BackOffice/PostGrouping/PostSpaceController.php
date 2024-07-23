<?php

namespace App\Http\Controllers\BackOffice\PostGrouping;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Enums\Posts\TemplatesEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\PostGrouping\PostSpaceRequest;
use App\Http\Resources\BackOffice\PostGrouping\PostSpaceCollection;
use App\Http\Resources\BackOffice\PostGrouping\PostSpaceResource;
use App\Models\BackOffice\PostGrouping\PostCategory;
use App\Models\BackOffice\PostGrouping\PostSpace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class PostSpaceController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, PostSpace::class);

        $jsGrid_Controller = parent::getJsGridType(PostSpace::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("post_grouping/spaces");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'title'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new PostSpaceRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ParentId->dbName(), __("thisApp.AdminPages.PostGrouping.ParentId"));
        $options = DropdownListCreater::makeByModel(PostCategory::class, TableEnum::Title->dbName())
            ->prepend("", -1)
            ->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $attr = $attributes[TableEnum::ParentId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value > -1;}", trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'start');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Title->dbName(), __('general.Title'));
        $fieldMaker->makeField_Text();
        $attr = $attributes[TableEnum::Title->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Template->dbName(), __("thisApp.AdminPages.PostGrouping.Template"));
        $options = DropdownListCreater::makeByArray(TemplatesEnum::translatedArray())
            ->prepend("", "")
            ->useLable("name", "key")->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $attr = $attributes[TableEnum::Template->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value != '';}", trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Description->dbName(), __('thisApp.AdminPages.PostGrouping.Description'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsPublicSpace->dbName(), __('thisApp.AdminPages.PostGrouping.IsPublicSpace'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsActive->dbName(), __('general.isActive'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PrivateNote->dbName(), __('thisApp.PrivateNote'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.PostGrouping.PostSpaces.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\PostGrouping\PostSpaceRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(PostSpaceRequest $request): JsonResponse
    {
        try {
            $item = new PostSpace();

            $item->fill($request->all());

            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new PostSpaceResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     */
    public function show(PostSpace $postSpace)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostSpace $postSpace)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\PostGrouping\PostSpaceRequest $request
     * @param \App\Models\BackOffice\PostGrouping\PostSpace $postSpace
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(PostSpaceRequest $request, PostSpace $postSpace): JsonResponse
    {

        try {

            $item = PostSpace::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());
            $item->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new PostSpaceResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\PostGrouping\PostSpaceRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(PostSpaceRequest $request): JsonResponse
    {
        if ($item = PostSpace::find($request->input(TableEnum::Id->dbName()))) {

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

        $this->authorize(PermissionAbilityEnum::viewAny->name, PostSpace::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new PostSpaceCollection(
            PostSpace::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
