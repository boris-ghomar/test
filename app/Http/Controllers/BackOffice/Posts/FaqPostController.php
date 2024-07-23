<?php

namespace App\Http\Controllers\BackOffice\Posts;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\Enums\Database\Tables\PostsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Posts\FaqPostRequest;
use App\Http\Resources\BackOffice\Posts\FaqPostCollection;
use App\Http\Resources\BackOffice\Posts\FaqPostResource;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\Posts\FaqPost;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;


class FaqPostController extends SuperJsGridController
{
    use AddAttributesPad;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, FaqPost::class);

        $jsGrid_Controller = new jsGrid_Controller("jsGrid", jsGrid_Controller::jsGridType_EditDelete);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("posts/faq");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'title'])));

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), __('general.ID'));
        $fieldMaker->makeField_Number();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "130");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new FaqPostRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Title->dbName(), __('general.Title'));
        $fieldMaker->makeField_Textarea();
        $attr = $attributes[TableEnum::Title->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PostSpaceId->dbName(), __("thisApp.PostSpace"));
        $options = DropdownListCreater::makeByModelQuery(PostSpace::Faqs(), PostGroupsTableEnum::Title->dbName())
            ->prepend("", -1)
            ->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $attr = $attributes[TableEnum::PostSpaceId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value > 0;}", trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ShortenedContentForTable->dbName(), __('general.Content'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsPublished->dbName(), __('general.IsPublished'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Views->dbName(), __('thisApp.Views'));
        $fieldMaker->makeField_NumberRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PrivateNote->dbName(), __('thisApp.PrivateNote'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TimestampsEnum::CreatedAt->dbName(), __('general.CreatedAt'));
        $fieldMaker->makeField_DateRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::AuthorId->dbName(), __("thisApp.AdminPages.Posts.Author"));
        $options = DropdownListCreater::makeByModel(Personnel::class, UsersTableEnum::Username->dbName())
            ->prepend("", -1)
            ->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $attr = $attributes[TableEnum::AuthorId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value > 0;}", trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ContentUpdatedAt->dbName(), __('general.UpdatedAt'));
        $fieldMaker->makeField_DateRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::EditorId->dbName(), __("thisApp.AdminPages.Posts.EditedBy"));
        $options = DropdownListCreater::makeByModel(Personnel::class, UsersTableEnum::Username->dbName())
            ->prepend("", -1)
            ->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $jsGrid_Controller->putField($fieldMaker);

        if (User::authUser()->can(PermissionAbilityEnum::update->name, FaqPost::class)) {

            $fieldMaker = new jsGrid_FieldMaker('edit_btn', __('general.buttons.FullEdit'));
            $fieldMaker->makeField_Text();
            $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_ShowOnly);
            $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
            $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
            $jsGrid_Controller->putField($fieldMaker);
        }

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


        return view('hhh.BackOffice.pages.Posts.FaqPosts.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize(PermissionAbilityEnum::create->name, FaqPost::class);

        $postSpaceCollection = DropdownListCreater::makeByModelQuery(PostSpace::Faqs(), PostGroupsTableEnum::Title->dbName(), PostGroupsTableEnum::Id->dbName())
            ->get();

        $data = [
            'formAction'            => AdminPublicRoutesEnum::Posts_FaqCreate->route(),
            'PostsTableEnum'        => TableEnum::class,
            'postSpaceCollection'   => $postSpaceCollection,
        ];

        return view('hhh.BackOffice.pages.Posts.FaqPosts.Create.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Posts\FaqPostRequest $request
     * @return \\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(FaqPostRequest $request): Redirector|RedirectResponse
    {
        try {

            $faqPost = new FaqPost();

            $tabpanel = $request->input('_tabpanel');

            if ($tabpanel == "Content") {

                $faqPost->fill($request->only(
                    TableEnum::PostSpaceId->dbName(),
                    TableEnum::Title->dbName(),
                ));

                $faqPost[TableEnum::AuthorId->dbName()] = auth()->user()->id;

                $faqPost->save();
            }
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->withErrors([$th->getMessage()]);
        }

        return redirect($faqPost->EditUrl)
            ->with('success', trans('PagesContent_PostForm.messages.SavedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BackOffice\Posts\FaqPost $faqPost
     * @param  ?string $slug
     * @return \Illuminate\Contracts\View\View|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function show(FaqPost $faqPost, ?string $slug)
    {
        // Placed in App\Http\Controllers\Site\DisplayContent\PostShowController
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BackOffice\Posts\FaqPost $faqPost
     * @return void
     */
    public function edit(FaqPost $faqPost)
    {
        $this->authorize(PermissionAbilityEnum::update->name, FaqPost::class);

        $postId = $faqPost->getAttribute(TableEnum::Id->dbName());

        $data = [
            'formAction'                => AdminPublicRoutesEnum::Posts_FaqEdit->route(['faqPost' => $postId]),
            'itemData'                  => $faqPost,
            'id'                        => $postId,
            'PostsTableEnum'            => TableEnum::class,
        ];

        return view('hhh.BackOffice.pages.Posts.FaqPosts.FullEdit.index', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\Posts\FaqPostRequest $request
     * @param \App\Models\BackOffice\Posts\FaqPost $faqPost
     * @return mixed
     */
    public function update(FaqPostRequest $request, FaqPost $faqPost)
    {
        $isApiRequest = $request->is('api/*');

        /** @var FaqPost $faqPost */
        $faqPost = FaqPost::find($request->input(TableEnum::Id->dbName()));

        if ($isApiRequest) {

            $faqPost->fill($request->except(TableEnum::Content->dbName(), TableEnum::MetaDescription->dbName()));
            return $this->updateViaApiRequest($faqPost);
        } else {
            $faqPost->fill($request->all());
            return $this->updateViaWebRequest($request, $faqPost);
        }
    }

    /**
     * Update the specified incoming resource from api route in storage.
     *
     * @param  \App\Models\BackOffice\Posts\FaqPost $faqPost
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    private function updateViaApiRequest(FaqPost $faqPost): JsonResponse
    {

        try {

            $contentAttributes = [
                TableEnum::Title->dbName(),
            ];

            if ($faqPost->isDirty($contentAttributes) && $faqPost->getAttribute(TableEnum::IsPublished->dbName())) {
                $faqPost[TableEnum::ContentUpdatedAt->dbName()] = \Carbon\Carbon::now();
                $faqPost[TableEnum::EditorId->dbName()] = auth()->user()->id;
            }

            $faqPost->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new FaqPostResource($faqPost), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Update the specified incoming resource from web route in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Models\BackOffice\Posts\FaqPost $faqPost
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    private function updateViaWebRequest(Request $request, FaqPost $faqPost)
    {

        try {

            $contentField = TableEnum::Content->dbName();
            $contentFieldHtml = $contentField . '_html';

            /**
             * Register large fields to avoid of Laravel playload saving error.
             *
             * When large data is returned back to the user,
             * Laravel tries to store this data in the playload column of
             * the sessions table, which causes the user to drop the database
             * and cause a database error.
             */
            $largeFields = [$contentField, $contentFieldHtml];

            $tabpanel = $request->input('_tabpanel');

            if ($tabpanel == "Content") {

                $contentAttributes = [
                    TableEnum::Title->dbName(),
                    TableEnum::Content->dbName(),
                ];

                if ($faqPost->isDirty($contentAttributes) && $faqPost->getAttribute(TableEnum::IsPublished->dbName())) {
                    $faqPost[TableEnum::ContentUpdatedAt->dbName()] = \Carbon\Carbon::now();
                    $faqPost[TableEnum::EditorId->dbName()] = auth()->user()->id;
                }

                $faqPost->save();
            } else if ($tabpanel == "MainPhoto") {

                /************** File ******************/
                $photoField = TableEnum::MainPhoto->dbName();
                $lastFile = $faqPost->getMainPhotoFileAssistant(false);
                $storedFileName = $lastFile->storeUploadedFile($request, $photoField);

                if ($storedFileName != null)
                    $faqPost[$photoField] = $storedFileName;
                /************** File END ******************/
                $faqPost->save();
            } else if ($tabpanel == "SEO") {

                $faqPost->save();
            }
        } catch (\Throwable $th) {
            return redirect()->back()->withInput($request->except($largeFields))->withErrors([$th->getMessage()]);
        }

        return redirect()->back()->with('success', trans('PagesContent_PostForm.messages.UpdatesSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Posts\FaqPostRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(FaqPostRequest $request): JsonResponse
    {
        if ($item = FaqPost::find($request->input(TableEnum::Id->dbName()))) {

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

        $this->authorize(PermissionAbilityEnum::viewAny->name, FaqPost::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new FaqPostCollection(
            FaqPost::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
