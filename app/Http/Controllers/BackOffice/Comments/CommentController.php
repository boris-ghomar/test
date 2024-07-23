<?php

namespace App\Http\Controllers\BackOffice\Comments;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\CommentsTableEnum as TableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Requests\BackOffice\Comments\CommentRequest;
use App\Http\Resources\BackOffice\Comments\CommentCollection;
use App\Models\BackOffice\Comments\Comment;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends SuperCommentController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, Comment::class);

        $jsGrid_Controller = parent::getJsGridType(Comment::class, jsGrid_Controller::jsGridType_EditDelete);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("comments/management");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, trans('confirm.Delete.simple'));

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $attributes = (new CommentRequest())->attributes();

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), __("thisApp.CommentId"));
        $fieldMaker->makeField_Number();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, 130);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PostId->dbName(), __("thisApp.PostId"));
        $fieldMaker->makeField_Number();
        $attr = TableEnum::PostId->dbName();
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_min, 1, trans('validation.min.numeric', ['attribute' => $attr, 'min' => 1]));
        $fieldMaker->setItemProperties($fieldMaker::field_Width, 130);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker("CommentLink", __("thisApp.CommentLink"));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_ShowOnly);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('OwnerUsername', __("thisApp.Owner"));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::UserId->dbName(), __("thisApp.UserId"));
        $fieldMaker->makeField_Number();
        $attr = $attributes[TableEnum::UserId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_min, 1, trans('validation.min.numeric', ['attribute' => $attr, 'min' => 1]));
        $fieldMaker->setItemProperties($fieldMaker::field_Width, 130);
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('displayName', __("thisApp.AdminPages.Comments.DisplayName"));
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_ShowOnly);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Comment->dbName(), __("thisApp.PostActions.Comment"));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, 300);
        $attr = $attributes[TableEnum::Comment->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker('answer', __("thisApp.AdminPages.Comments.Answer"));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, 300);
        $fieldMaker->setItemProperties($fieldMaker::field_isSorting, false);
        $fieldMaker->setItemProperties($fieldMaker::field_isFiltering, false);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsApproved->dbName(), __("thisApp.IsApproved"));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TimestampsEnum::CreatedAt->dbName(), __('general.CreatedAt'));
        $fieldMaker->makeField_DateRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ApprovedBy->dbName(), __("thisApp.ApprovedBy"));
        $options = DropdownListCreater::makeByModel(Personnel::class, UsersTableEnum::Username->dbName())
            ->prepend("", -1)
            ->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TimestampsEnum::UpdatedAt->dbName(), __('general.UpdatedAt'));
        $fieldMaker->makeField_DateRange();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $fieldMaker->setItemProperties($fieldMaker::field_css, 'ltr');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.Comments.CommentsManagement.index', $data);
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
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\Comments\CommentRequest $request
     * @param  \App\Models\BackOffice\Comments\Comment $comment
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(CommentRequest $request, Comment $comment): JsonResponse
    {
        return parent::updateComment($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\Comments\CommentRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(CommentRequest $request): JsonResponse
    {
        return parent::destroyComment($request);
    }

    /**
     * Return the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiIndex(Request $request)
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, Comment::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new CommentCollection(
            Comment::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }
}
