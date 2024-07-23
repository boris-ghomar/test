<?php

namespace App\Http\Controllers\Site\UserActions;

use App\Enums\AccessControl\PostActionsEnum;
use App\Enums\Database\Tables\CommentsTableEnum as TableEnum;
use App\Enums\Database\Tables\CommentsTableEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\Enums\Database\Tables\PostSpacesPermissionsTableEnum;
use App\Enums\Database\Tables\PostsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Enums\Settings\DynamicDataVariablesEnum;
use App\Enums\UserActions\CommentableTypesEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Models\BackOffice\PostGrouping\PostSpacePermission;
use App\Models\BackOffice\Posts\Post;
use App\Models\BackOffice\Settings\DynamicData;
use App\Models\BackOffice\Settings\Setting;
use App\Models\Site\UserActions\Comment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommentController extends UserActionController
{

    /**
     * Determine the logic for perform action
     *
     * @param  \Illuminate\Http\Request $request
     * @param mixed $key
     * @return JsonResponse
     */
    public function actionLogic(Request $request, mixed $key): JsonResponse
    {
        $commentText = Str::of($request->input('comment'))->trim()->toString();
        $commentableType = $key->commentableType;
        $commentableId = $key->commentableId;
        $postId = $key->postId;
        $userId = User::authUser()[UsersTableEnum::Id->dbName()];

        $comment = Comment::where(TableEnum::UserId->dbName(), $userId)
            ->where(TableEnum::CommentableType->dbName(), $commentableType)
            ->where(TableEnum::CommentableId->dbName(), $commentableId)
            ->where(TableEnum::Comment->dbName(), $commentText)
            ->first();

        if (!is_null($comment))
            return JsonResponseHelper::errorResponse('thisApp.Errors.repeatedcomment', __('thisApp.Errors.repeatedcomment'), HttpResponseStatusCode::UnprocessableEntity->value);

        $needToApprove = $request->user()->isPersonnel() ? false : Setting::get(AppSettingsEnum::CommentApproval, true);

        $comment = new Comment();
        $comment->forceFill([

            TableEnum::UserId->dbName()             => $userId,
            TableEnum::PostId->dbName()             => $postId,
            TableEnum::CommentableType->dbName()    => $commentableType,
            TableEnum::CommentableId->dbName()      => $commentableId,
            TableEnum::Comment->dbName()            => $commentText,
            TableEnum::IsApproved->dbName()         => $needToApprove ? 0 : 1,
        ]);

        $comment->save();
        $commentable = $comment->commentable;

        $data = [
            'commentsCount' =>  number_format($commentable->comments()->Approved()->count()),
            'reload' => !$needToApprove,
            'anchorLink' => $needToApprove ? "" : $comment->DisplayUrl,
        ];
        $successMessage = $needToApprove ? __('thisApp.messages.commentRegisteredSuccessfullyApproval') : __('thisApp.messages.commentRegisteredSuccessfully');
        $commentRegistrationExplanation = DynamicData::get(DynamicDataVariablesEnum::Comment_CommentRegistrationExplanation);
        if (!empty($commentRegistrationExplanation))
            $successMessage .= '<br><br>' . $commentRegistrationExplanation;

        return JsonResponseHelper::successResponse($data, $successMessage, HttpResponseStatusCode::Accepted->value);
    }

    /**
     * Determine internal validation in child class
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User $user
     * @param  object $key
     * @return object
     */
    protected function internalValidation(Request $request, User $user, object $key): object
    {

        $commentText = Str::of($request->input('comment'))->trim()->toString();
        if (empty($commentText))
            return JsonResponseHelper::errorResponse('thisApp.Errors.emptyComment', __('thisApp.Errors.emptyComment'), HttpResponseStatusCode::UnprocessableEntity->value);

        if (isset($key->userId) && isset($key->commentableType) && isset($key->commentableId)) {

            if ($key->userId != $user[UsersTableEnum::Id->dbName()])
                return JsonResponseHelper::errorResponse(null, null, HttpResponseStatusCode::Unauthorized->value);

            // Check client comment permission

            if ($user->isPersonnel())
                return $key;

            $commentableType = $key->commentableType;
            $commentableId = $key->commentableId;

            $post = null;

            if ($commentableType === CommentableTypesEnum::Post->name) {

                $post = Post::select(PostsTableEnum::Id->dbName(), PostsTableEnum::PostSpaceId->dbName())
                    ->where(PostsTableEnum::Id->dbName(), $commentableId)
                    ->first();
            } else if ($commentableType === CommentableTypesEnum::Comment->name) {

                $comment = Comment::select(CommentsTableEnum::Id->dbName(), CommentsTableEnum::CommentableType->dbName(), CommentsTableEnum::CommentableId->dbName())
                    ->where(CommentsTableEnum::Id->dbName(), $commentableId)
                    ->first();

                $post = $comment->post()
                    ->select(PostsTableEnum::Id->dbName(), PostsTableEnum::PostSpaceId->dbName())
                    ->first();
            }

            if (!is_null($post)) {

                $postSpaceIdKey = PostGroupsTableEnum::Id->dbName();
                $clientCategoryIdKey = RolesTableEnum::Id->dbName();

                $postSpaceId = $post->postSpace()->select($postSpaceIdKey)->first()->$postSpaceIdKey;
                $clientCategoryId = $user->role()->select($clientCategoryIdKey)->first()->$clientCategoryIdKey;

                $permission = PostSpacePermission::ActivePermissions()
                    ->where(PostSpacesPermissionsTableEnum::PostSpaceId->dbName(), $postSpaceId)
                    ->where(PostSpacesPermissionsTableEnum::ClientCategoryId->dbName(), $clientCategoryId)
                    ->where(PostSpacesPermissionsTableEnum::PostAction->dbName(), PostActionsEnum::Comment->name);

                if ($permission->exists()) {
                    return $key;
                } else {
                    return JsonResponseHelper::errorResponse('thisApp.Errors.commentBlock', __('thisApp.Errors.commentBlock'), HttpResponseStatusCode::BadRequest->value);
                }
            }
        }


        // Input data is not correct or incomplete
        return JsonResponseHelper::errorResponse(null, null, HttpResponseStatusCode::BadRequest->value);
    }
}
