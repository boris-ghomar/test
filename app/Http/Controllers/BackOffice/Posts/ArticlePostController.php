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
use App\HHH_Library\general\php\Enums\SeoMetaTagsEnum;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\HHH_Library\QuillEditor\QuillEditorHelper;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\Posts\ArticlePostRequest;
use App\Http\Resources\BackOffice\Posts\ArticlePostCollection;
use App\Http\Resources\BackOffice\Posts\ArticlePostResource;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\Posts\ArticlePost;
use App\Models\BackOffice\Posts\Post;
use App\Models\User;
use App\Rules\General\Database\ExistsInModel;
use App\Rules\General\Database\UniqueInModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;

class ArticlePostController extends SuperJsGridController
{
    use AddAttributesPad;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, ArticlePost::class);

        $jsGrid_Controller = new jsGrid_Controller("jsGrid", jsGrid_Controller::jsGridType_EditDelete);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("posts/article");
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

        $attributes = (new ArticlePostRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Title->dbName(), __('general.Title'));
        $fieldMaker->makeField_Textarea();
        $attr = $attributes[TableEnum::Title->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PostSpaceId->dbName(), __("thisApp.PostSpace"));
        $options = DropdownListCreater::makeByModelQuery(PostSpace::Articles(), PostGroupsTableEnum::Title->dbName())
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

        if (User::authUser()->can(PermissionAbilityEnum::update->name, ArticlePost::class)) {

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


        return view('hhh.BackOffice.pages.Posts.ArticlePosts.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize(PermissionAbilityEnum::create->name, ArticlePost::class);

        $articlePost = new ArticlePost();
        $postSpaceCollection = DropdownListCreater::makeByModelQuery(PostSpace::Articles(), PostGroupsTableEnum::Title->dbName(), PostGroupsTableEnum::Id->dbName())
            ->get();

        $data = [
            'formAction' => AdminPublicRoutesEnum::Posts_ArticlesCreate->route(),
            'PostsTableEnum' => TableEnum::class,
            'mainPhotoFileAssistant' => $articlePost->getMainPhotoFileAssistant(),
            'postSpaceCollection' => $postSpaceCollection,
        ];

        return view('hhh.BackOffice.pages.Posts.ArticlePosts.Create.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Posts\ArticlePostRequest $request
     * @return \\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(ArticlePostRequest $request): Redirector|RedirectResponse
    {
        try {

            $articlePost = new ArticlePost();

            $tabpanel = $request->input('_tabpanel');

            if ($tabpanel == "Content") {

                $articlePost->fill($request->only(
                    TableEnum::PostSpaceId->dbName(),
                    TableEnum::Title->dbName(),
                ));

                $articlePost[TableEnum::AuthorId->dbName()] = auth()->user()->id;

                $articlePost->save();
            }
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->withErrors([$th->getMessage()]);
        }

        return redirect($articlePost->EditUrl)
            ->with('success', trans('PagesContent_PostForm.messages.SavedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BackOffice\Posts\ArticlePost $articlePost
     * @param  ?string $slug
     * @return \Illuminate\Contracts\View\View|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function show(ArticlePost $articlePost, ?string $slug)
    {
        // Placed in App\Http\Controllers\Site\DisplayContent\PostShowController
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BackOffice\Posts\ArticlePost $articlePost
     * @return void
     */
    public function edit(ArticlePost $articlePost)
    {
        $this->authorize(PermissionAbilityEnum::update->name, ArticlePost::class);

        $postId = $articlePost->getAttribute(TableEnum::Id->dbName());

        $editor = QuillEditorHelper::setContentViaFile($postId);

        $data = [
            'formAction' => AdminPublicRoutesEnum::Posts_ArticlesEdit->route(['articlePost' => $postId]),
            'itemData' => $articlePost,
            'id' => $postId,
            'PostsTableEnum' => TableEnum::class,
            'mainPhotoFileAssistant' => $articlePost->getMainPhotoFileAssistant(),

            'editorData' => $editor->getDataList(true),


        ];

        return view('hhh.BackOffice.pages.Posts.ArticlePosts.FullEdit.index', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BackOffice\Posts\ArticlePostRequest $request
     * @param \App\Models\BackOffice\Posts\ArticlePost $articlePost
     * @return mixed
     */
    public function update(ArticlePostRequest $request, ArticlePost $articlePost)
    {
        $isApiRequest = $request->is('api/*');

        /** @var ArticlePost $articlePost */
        $articlePost = ArticlePost::find($request->input(TableEnum::Id->dbName()));
        $articlePost->fill($request->except(TableEnum::Content->dbName()));

        return $isApiRequest ? $this->updateViaApiRequest($articlePost)
            : $this->updateViaWebRequest($request, $articlePost);
    }

    /**
     * Update the specified incoming resource from api route in storage.
     *
     * @param  \App\Models\BackOffice\Posts\ArticlePost $articlePost
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    private function updateViaApiRequest(ArticlePost $articlePost): JsonResponse
    {

        try {

            $contentAttributes = [
                TableEnum::Title->dbName(),
            ];

            if ($articlePost->isDirty($contentAttributes) && $articlePost->getAttribute(TableEnum::IsPublished->dbName())) {
                $articlePost[TableEnum::ContentUpdatedAt->dbName()] = \Carbon\Carbon::now();
                $articlePost[TableEnum::EditorId->dbName()] = auth()->user()->id;
            }

            $articlePost->save();

            /**
             * Keep this check for the end, saveable content is saved
             * and then checked to see if the post is publishable or not.
             */
            $publishCheck = $this->publishCheck($articlePost);
            if ($publishCheck !== true) {

                $this->unpublishPost($articlePost);

                if (!is_string($publishCheck) && !is_array($publishCheck))
                    $publishCheck = $publishCheck->toArray();

                return JsonResponseHelper::errorResponse(null, $publishCheck, HttpResponseStatusCode::NotAcceptable->value);
            }
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new ArticlePostResource($articlePost), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Update the specified incoming resource from web route in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Models\BackOffice\Posts\ArticlePost $articlePost
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    private function updateViaWebRequest(Request $request, ArticlePost $articlePost)
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

                $editor = QuillEditorHelper::setContent($request->input($contentField), $request->input($contentFieldHtml));

                $articlePost[$contentField] = $editor->getText();

                $contentAttributes = [
                    TableEnum::Title->dbName(),
                    TableEnum::Content->dbName(),
                ];

                if ($articlePost->isDirty($contentAttributes) && $articlePost->getAttribute(TableEnum::IsPublished->dbName())) {
                    $articlePost[TableEnum::ContentUpdatedAt->dbName()] = \Carbon\Carbon::now();
                    $articlePost[TableEnum::EditorId->dbName()] = auth()->user()->id;
                }

                $postId = $articlePost->getAttribute(TableEnum::Id->dbName());

                if ($editor->savePostContent($postId)) {

                    $errors = $this->validateContentTab($articlePost);
                    if ($errors !== true) {

                        return redirect()->back()
                            ->withInput($request->except($largeFields))
                            ->withErrors(is_string($errors) ? [$errors] : $errors);
                    }

                    $articlePost->save();
                } else {
                    return redirect()->back()->withInput($request->except($largeFields))->withErrors([__('PagesContent_PostForm.messages.SavedFailed')]);
                }
            } else if ($tabpanel == "MainPhoto") {

                /************** File ******************/
                $photoField = TableEnum::MainPhoto->dbName();
                $lastFile = $articlePost->getMainPhotoFileAssistant(false);
                $storedFileName = $lastFile->storeUploadedFile($request, $photoField);

                if ($storedFileName != null)
                    $articlePost[$photoField] = $storedFileName;
                /************** File END ******************/
                $articlePost->save();
            } else if ($tabpanel == "SEO") {

                $articlePost->save();
            }

            /**
             * Keep this check for the end, saveable content is saved
             * and then checked to see if the post is publishable or not.
             */
            $publishCheck = $this->publishCheck($articlePost);
            if ($publishCheck !== true) {

                $this->unpublishPost($articlePost);

                return redirect()->back()
                    ->withInput($request->except($largeFields))
                    ->withErrors(is_string($publishCheck) ? [$publishCheck] : $publishCheck);
            }
        } catch (\Throwable $th) {
            return redirect()->back()->withInput($request->except($largeFields))->withErrors([$th->getMessage()]);
        }

        return redirect()->back()->with('success', trans('PagesContent_PostForm.messages.UpdatesSuccessfully'));
    }


    /**
     * Web content validation will be down in contoller, beacause:
     *
     * When large data is returned back to the user,
     * Laravel tries to store this data in the playload column of
     * the sessions table, which causes the user to drop the database
     * and cause a database error.
     *
     *
     * @param  \App\Models\BackOffice\Posts\ArticlePost $articlePost
     * @return void
     */
    private function validateContentTab(ArticlePost $articlePost): mixed
    {
        $attributes = (new ArticlePostRequest())->attributes();

        $primaryKey = TableEnum::Id->dbName();

        $post = $articlePost->replicate();
        $postId = $post[$primaryKey] = $articlePost->getAttribute($primaryKey);

        $metaTitle = SeoMetaTagsEnum::MetaTitle;

        $validator = Validator::make($articlePost->getAttributes(), [

            TableEnum::PostSpaceId->dbName() => [
                'required', 'numeric',
                new ExistsInModel(PostSpace::class, PostGroupsTableEnum::Id->dbName())
            ],

            TableEnum::Title->dbName() => [
                'required',
                new UniqueInModel(Post::class, $postId),
                'min:' . $metaTitle->minLength(),
                'max:' . $metaTitle->maxLength(),
            ],

            TableEnum::IsPublished->dbName() => ['required', 'boolean'],

        ], [], $attributes)->stopOnFirstFailure(true);

        if ($validator->fails())
            return $validator->errors();

        return true;
    }

    /**
     * If the publish post option is checked,
     * it checks whether the post is ready to be published or not.
     *
     * @param  \App\Models\BackOffice\Posts\ArticlePost $articlePost
     * @return mixed
     */
    private function publishCheck(ArticlePost $articlePost): mixed
    {
        if ($articlePost->getAttribute(TableEnum::IsPublished->dbName())) {

            $attributes = (new ArticlePostRequest())->attributes();


            $primaryKey = TableEnum::Id->dbName();

            // Clone post data to avoid affecting the original model
            $post = $articlePost->replicate();
            $postId = $post[$primaryKey] = $articlePost->getAttribute($primaryKey);

            $metaTitle = SeoMetaTagsEnum::MetaTitle;
            $metaDescription = SeoMetaTagsEnum::MetaDescription;

            $validator = Validator::make($post->getAttributes(), [

                TableEnum::PostSpaceId->dbName() => [
                    'required', 'numeric',
                    new ExistsInModel(PostSpace::class, PostGroupsTableEnum::Id->dbName())
                ],

                TableEnum::Title->dbName() => [
                    'required',
                    new UniqueInModel(Post::class, $postId),
                    'min:' . $metaTitle->minLength(),
                    'max:' . $metaTitle->maxLength(),
                ],

                TableEnum::MetaDescription->dbName() => [
                    'bail', 'required',
                    'min:' . $metaDescription->minLength(),
                    'max:' . $metaDescription->maxLength(),
                ],

            ], [], $attributes)->stopOnFirstFailure(true);

            if ($validator->fails())
                return $validator->errors();

            // MainPhotoRequired
            if (is_null($post[TableEnum::MainPhoto->dbName()]))
                return __('thisApp.Errors.Posts.MainPhotoRequired');

            // Minimum required words
            $editor = QuillEditorHelper::setContentViaFile($postId);

            $minWordsCount = 300;
            $wordsCount = $editor->getWordsCount();
            if ($wordsCount < $minWordsCount) {

                return __('thisApp.Errors.Posts.MinRequiredWords', [
                    'minWordsCount' => $minWordsCount,
                    'wordsCount' => $wordsCount,
                ]);
            }
        }

        return true;
    }

    /**
     * Unpublish post
     *
     * @param  \App\Models\BackOffice\Posts\ArticlePost $articlePost
     * @return void
     */
    private function unpublishPost(ArticlePost $articlePost): void
    {
        $isPublishedKey = TableEnum::IsPublished->dbName();

        if (isset($articlePost[$isPublishedKey]))
            $articlePost[$isPublishedKey] = 0;

        if ($post = ArticlePost::find($articlePost->getAttribute(TableEnum::Id->dbName()))) {

            $post[$isPublishedKey] = 0;
            $post->Save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Posts\ArticlePostRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(ArticlePostRequest $request): JsonResponse
    {
        if ($item = ArticlePost::find($request->input(TableEnum::Id->dbName()))) {

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

        $this->authorize(PermissionAbilityEnum::viewAny->name, ArticlePost::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new ArticlePostCollection(
            ArticlePost::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
