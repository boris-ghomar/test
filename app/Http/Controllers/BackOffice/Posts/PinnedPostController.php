<?php

namespace App\Http\Controllers\BackOffice\Posts;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\Enums\Database\Tables\PostsTableEnum as TableEnum;
use App\Enums\Posts\TemplatesEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Posts\PinnedPostRequest;
use App\Http\Resources\BackOffice\Posts\PinnedPostCollection;
use App\Http\Resources\BackOffice\Posts\PinnedPostResource;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\Posts\PinnedPost;
use App\Models\BackOffice\Posts\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class PinnedPostController extends SuperJsGridController
{
    use AddAttributesPad;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, PinnedPost::class);

        $jsGrid_Controller = new jsGrid_Controller("jsGrid");
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("posts/pinned");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, trans('confirm.Delete.simple'));

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), __('general.ID'));
        $fieldMaker->makeField_Number();
        $fieldMaker->setItemProperties($fieldMaker::field_isEditing, False);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "130");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new PinnedPostRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Title->dbName(), __('general.Title'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PostGroupsTableEnum::Template->dbName(), __("thisApp.AdminPages.PostGrouping.Template"));
        $options = DropdownListCreater::makeByArray(TemplatesEnum::translatedArray())
            ->useLable("name", "key")->prepend("", "")->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PostSpaceId->dbName(), __("thisApp.PostSpace"));
        $options = DropdownListCreater::makeByModel(PostSpace::class, PostGroupsTableEnum::Title->dbName())
            ->prepend("", -1)
            ->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ShortenedContentForTable->dbName(), __('general.Content'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PinNumber->dbName(), __('thisApp.AdminPages.Posts.PinNumber'));
        $fieldMaker->makeField_NumberRange();
        $attr = $attributes[TableEnum::PinNumber->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_min, 1, trans('validation.min.numeric', ['attribute' => $attr, 'min' => 1]));
        $fieldMaker->setItemProperties($fieldMaker::field_Width, '130');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_isInserting, false);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Views->dbName(), __('thisApp.Views'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PrivateNote->dbName(), __('thisApp.PrivateNote'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_isInserting, false);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('view_btn', __('general.buttons.View'));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_ShowOnly);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.Posts.PinnedPosts.index', $data);
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
     * @param  \App\Http\Requests\BackOffice\Posts\PinnedPostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PinnedPostRequest $request): JsonResponse
    {
        try {

            $isPinnedCol = TableEnum::IsPinned->dbName();
            $pinNumberCol = TableEnum::PinNumber->dbName();

            if (PinnedPost::find($request->input(TableEnum::Id->dbName())))
                return JsonResponseHelper::errorResponse('thisApp.Errors.Posts.AlreadyPinned', __('thisApp.Errors.Posts.AlreadyPinned'), HttpResponseStatusCode::UnprocessableEntity->value);


            $item = Post::find($request->input(TableEnum::Id->dbName()));

            if (!is_null($item)) {

                $lastPinnedPost = PinnedPost::select($pinNumberCol)->orderBy($pinNumberCol, 'desc')->first();

                $item->$isPinnedCol = 1;
                $item->$pinNumberCol = (is_null($lastPinnedPost)) ? 1 : $lastPinnedPost->$pinNumberCol + 1;

                $item->save();
            } else
                return JsonResponseHelper::errorResponse('thisApp.Errors.Posts.PostNotExist', __('thisApp.Errors.Posts.PostNotExist'), HttpResponseStatusCode::NotFound->value);
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new PinnedPostResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BackOffice\Posts\PinnedPost $pinnedPost
     * @return \Illuminate\Contracts\View\View|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function show(PinnedPost $pinnedPost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BackOffice\Posts\PinnedPost $pinnedPost
     * @return void
     */
    public function edit(PinnedPost $pinnedPost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\Posts\PinnedPostRequest $request
     * @param \App\Models\BackOffice\Posts\PinnedPost $pinnedPost
     * @return mixed
     */
    public function update(PinnedPostRequest $request, PinnedPost $pinnedPost)
    {
        try {

            $pinNumberCol = TableEnum::PinNumber->dbName();

            $item = PinnedPost::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());
            if ($isDirtyPinNumber = $item->isDirty($pinNumberCol))
                $lastPinNumber = $item->getOriginal($pinNumberCol);

            $item->save();

            if ($isDirtyPinNumber)
                $this->adjustPinNumbers($lastPinNumber, $item->$pinNumberCol);
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new PinnedPostResource($item), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Posts\PinnedPostRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(PinnedPostRequest $request): JsonResponse
    {
        if ($item = PinnedPost::find($request->input(TableEnum::Id->dbName()))) {

            $item[TableEnum::IsPinned->dbName()] = 0;
            $item[TableEnum::PinNumber->dbName()] = 0;
            $item->save();
            $this->adjustPinNumbers(0, 0);
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

        $this->authorize(PermissionAbilityEnum::viewAny->name, PinnedPost::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new PinnedPostCollection(
            PinnedPost::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }

    /**
     * Adjust items Pin Numbers
     *
     * @param  int $lastPinNumber
     * @param  int $currentPosition
     * @return void
     */
    private function adjustPinNumbers(int $lastPinNumber, int $currentPinNumber): void
    {
        $idCol = TableEnum::Id->dbName();
        $pinNumberCol = TableEnum::PinNumber->dbName();

        $updatedAtSort = ($currentPinNumber < $lastPinNumber) ? "desc" : "asc";

        $items = PinnedPost::select($idCol, $pinNumberCol)
            ->orderBy($pinNumberCol, 'asc')
            ->orderBy(TimestampsEnum::UpdatedAt->dbName(), $updatedAtSort)
            ->get();

        $pinNumber = 1;
        foreach ($items as $item) {

            $item->$pinNumberCol = $pinNumber;
            $item->save();

            $pinNumber++;
        }
    }
}
