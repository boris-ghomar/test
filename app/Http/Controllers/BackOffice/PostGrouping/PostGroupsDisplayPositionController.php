<?php

namespace App\Http\Controllers\BackOffice\PostGrouping;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\PostGrouping\PostGroupsDisplayPositionRequest;
use App\Http\Resources\BackOffice\PostGrouping\PostGroupsDisplayPositionCollection;
use App\Http\Resources\BackOffice\PostGrouping\PostGroupsDisplayPositionResource;
use App\Models\BackOffice\PostGrouping\PostCategory;
use App\Models\BackOffice\PostGrouping\PostGroupsDisplayPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class PostGroupsDisplayPositionController extends SuperJsGridController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, PostGroupsDisplayPosition::class);

        $jsGrid_Controller = parent::getJsGridType(PostGroupsDisplayPosition::class, jsGrid_Controller::jsGridType_Edit);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("post_grouping/post_groups_display_position");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'title'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new PostGroupsDisplayPositionRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ParentId->dbName(), __("thisApp.AdminPages.PostGrouping.ParentId"));
        $options = DropdownListCreater::makeByModel(PostCategory::class, TableEnum::Title->dbName())
            ->prepend(__('thisApp.WithoutParent'), 0)
            ->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', 0, 'number', $options);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'start');
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Title->dbName(), __('general.Title'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Position->dbName(), __('thisApp.AdminPages.PostGrouping.Position'));
        $fieldMaker->makeField_NumberRange();
        $attr = $attributes[TableEnum::Position->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_min, 1, trans('validation.min.numeric', ['attribute' => $attr, 'min' => 1]));
        $fieldMaker->setItemProperties($fieldMaker::field_Width, '100');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsActive->dbName(), __('general.isActive'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);

        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.PostGrouping.PostGroupsDisplayPosition.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\PostGrouping\PostGroupsDisplayPositionRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(PostGroupsDisplayPositionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PostGroupsDisplayPosition $postGroupsDisplayPosition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostGroupsDisplayPosition $postGroupsDisplayPosition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\PostGrouping\PostGroupsDisplayPositionRequest $request
     * @param \App\Models\BackOffice\PostGrouping\PostGroupsDisplayPosition $postGroupsDisplayPosition
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(PostGroupsDisplayPositionRequest $request, PostGroupsDisplayPosition $postGroupsDisplayPosition): JsonResponse
    {
        try {

            $item = PostGroupsDisplayPosition::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());
            if ($isDirtyPosition = $item->isDirty(TableEnum::Position->dbName()))
                $lastPosition = $item->getOriginal(TableEnum::Position->dbName());
            $item->save();

            if ($isDirtyPosition)
                $this->adjustOtherPositions($item, $lastPosition);
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new PostGroupsDisplayPositionResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Adjust Other items Positions
     *
     * @param  mixed $postGroupsDisplayPosition
     * @return void
     */
    private function adjustOtherPositions(PostGroupsDisplayPosition $postGroupsDisplayPosition, int $lastPosition): void
    {

        $idCol = TableEnum::Id->dbName();
        $positionCol = TableEnum::Position->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();

        $parentId = $postGroupsDisplayPosition->getAttribute($parentIdCol);
        $currentPosition = $postGroupsDisplayPosition->getAttribute($positionCol);

        $updatedAtSort = ($currentPosition < $lastPosition) ? "desc" : "asc";

        $parentChilds = PostGroupsDisplayPosition::where($parentIdCol, $parentId)
            ->select($idCol, $positionCol)
            ->orderBy($positionCol, 'asc')
            ->orderBy(TimestampsEnum::UpdatedAt->dbName(), $updatedAtSort)
            ->get();

        $position = 1;
        foreach ($parentChilds as $child) {

            if ($child[$positionCol] != $position) {

                $child[$positionCol] = $position;
                $child->save();
            }
            $position++;
        }
    }

    private function adjustSubsetPositions(int $parentId)
    {
        $idCol = TableEnum::Id->dbName();
        $positionCol = TableEnum::Position->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();


        $parentChilds = PostGroupsDisplayPosition::where($parentIdCol, $parentId)
            ->select($idCol, $positionCol)
            ->orderBy($positionCol, 'asc')
            ->orderBy(TimestampsEnum::UpdatedAt->dbName(), 'desc')
            ->get();

        $position = 1;
        foreach ($parentChilds as $child) {

            if ($child[$positionCol] != $position) {

                $child[$positionCol] = $position;
                $child->save();
            }
            $position++;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\PostGrouping\PostGroupsDisplayPositionRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(PostGroupsDisplayPositionRequest $request)
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, PostGroupsDisplayPosition::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        $this->adjustSubsetPositions($request->input(TableEnum::ParentId->dbName()));

        return new PostGroupsDisplayPositionCollection(
            PostGroupsDisplayPosition::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
