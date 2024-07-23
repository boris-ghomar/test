<?php

namespace App\Http\Controllers\Site\UserActions;

use App\Enums\AccessControl\PostActionsEnum;
use App\Enums\Database\Tables\CommentsTableEnum;
use App\Enums\Database\Tables\LikesTableEnum as TableEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\Enums\Database\Tables\PostSpacesPermissionsTableEnum;
use App\Enums\Database\Tables\PostsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\UserActions\LikableTypesEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Models\BackOffice\PostGrouping\PostSpacePermission;
use App\Models\BackOffice\Posts\Post;
use App\Models\Site\UserActions\Comment;
use App\Models\Site\UserActions\Like;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends UserActionController
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
        $likableType = $key->likableType;
        $likableId = $key->likableId;
        $userId = User::authUser()[UsersTableEnum::Id->dbName()];

        $like = Like::where(TableEnum::UserId->dbName(), $userId)
            ->where(TableEnum::LikableType->dbName(), $likableType)
            ->where(TableEnum::LikableId->dbName(), $likableId)
            ->first();

        if (is_null($like)) {

            $like = new Like();
            $like->forceFill([

                TableEnum::UserId->dbName()         => $userId,
                TableEnum::LikableType->dbName()    => $likableType,
                TableEnum::LikableId->dbName()      => $likableId,
            ]);

            $like->save();
            $likable = $like->likable;
        } else {
            // The user wants to delete the like
            $likable = $like->likable;
            $like->delete();
        }

        $data = [
            'likesCount' =>  number_format($likable->likes()->count()),
        ];

        return JsonResponseHelper::successResponse($data, null, HttpResponseStatusCode::Accepted->value);
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

        if (isset($key->userId) && isset($key->likableType) && isset($key->likableId)) {

            if ($key->userId != $user[UsersTableEnum::Id->dbName()])
                return JsonResponseHelper::errorResponse(null, null, HttpResponseStatusCode::Unauthorized->value);

            // Check client like permission

            if ($user->isPersonnel())
                return $key;

            $likableType = $key->likableType;
            $likableId = $key->likableId;

            $post = null;
            if ($likableType === LikableTypesEnum::Post->name) {

                $post = Post::select(PostsTableEnum::Id->dbName(), PostsTableEnum::PostSpaceId->dbName())
                    ->where(PostsTableEnum::Id->dbName(), $likableId)
                    ->first();
            } else if ($likableType === LikableTypesEnum::Comment->name) {

                $comment = Comment::select(CommentsTableEnum::Id->dbName(), CommentsTableEnum::CommentableType->dbName(), CommentsTableEnum::CommentableId->dbName())
                    ->where(CommentsTableEnum::Id->dbName(), $likableId)
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
                    ->where(PostSpacesPermissionsTableEnum::PostAction->dbName(), PostActionsEnum::Like->name);

                if ($permission->exists()) {
                    return $key;
                } else {
                    return JsonResponseHelper::errorResponse('thisApp.Errors.likeBlock', __('thisApp.Errors.likeBlock'), HttpResponseStatusCode::BadRequest->value);
                }
            }
        }


        // Input data is not correct or incomplete
        return JsonResponseHelper::errorResponse(null, null, HttpResponseStatusCode::BadRequest->value);
    }
}
