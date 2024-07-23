<?php

namespace App\Http\Controllers\BackOffice\Comments;

use App\Enums\Database\Tables\CommentsTableEnum as TableEnum;
use App\Enums\UserActions\CommentableTypesEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Resources\BackOffice\Comments\CommentResource;
use App\Models\BackOffice\Comments\Comment;
use App\Models\Site\UserActions\Comment as UserActionsComment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class SuperCommentController extends SuperJsGridController
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function updateComment(Request $request): JsonResponse
    {
        try {

            $item = Comment::find($request->input(TableEnum::Id->dbName()));

            $item->fill($request->all());

            $dirtyItems = [
                TableEnum::UserId->dbName(),
                TableEnum::Comment->dbName(),
                TableEnum::IsApproved->dbName(),
            ];

            $isApproved = $item[TableEnum::IsApproved->dbName()];

            if ($isApproved && $item->isDirty($dirtyItems))
                $item[TableEnum::ApprovedBy->dbName()] = User::authUser()->id;

            if (!$isApproved) {
                $item[TableEnum::ApprovedBy->dbName()] = null;
            }

            $isPostIdChanged = false;
            $postId = $request->input(TableEnum::PostId->dbName());
            if ($item->post->id != $postId) {

                $item[TableEnum::CommentableType->dbName()] = CommentableTypesEnum::Post->name;
                $item[TableEnum::CommentableId->dbName()] = $postId;
                $item[TableEnum::PostId->dbName()] = $postId;

                $isPostIdChanged = true;
            }

            $item->save();

            if ($isPostIdChanged){
                // Refresh model
                $item = Comment::find($item->id);
                $this->modifySubCommnetsPostId($item);
            }

            $this->storeCommnetAnswer($request, $item);
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new CommentResource($item->fresh()), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Modify post_id of sub-commnets
     *
     * @param  \App\Models\Site\UserActions\Comment $comment
     * @return void
     */
    private function modifySubCommnetsPostId(UserActionsComment $comment): void
    {

        $postIdCol = TableEnum::PostId->dbName();

        $postId = $comment->$postIdCol;

        $subComments = $comment->comments;

        foreach ($subComments as $subComment) {

            $subComment->$postIdCol = $postId;
            $subComment->save();

            $this->modifySubCommnetsPostId($subComment);
        }
    }

    /**
     * Store commnet answer.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Models\BackOffice\Comments\Comment $comment
     * @return void
     */
    private function storeCommnetAnswer(Request $request, Comment $comment): void
    {
        $answerText = Str::of($request->input('answer'))->trim()->toString();

        if (empty($answerText))
            return;

        $answerCommnet = $comment->adminAnswer;

        if (is_null($answerCommnet)) {

            $answerCommnet = new Comment();

            $answerCommnet->forceFill([
                TableEnum::UserId->dbName()             =>  auth()->user()->id,
                TableEnum::PostId->dbName()             =>  $comment->post->id,
                TableEnum::CommentableType->dbName()    =>  CommentableTypesEnum::Comment->name,
                TableEnum::CommentableId->dbName()      =>  $comment[TableEnum::Id->dbName()],
                TableEnum::Comment->dbName()            =>  $answerText,
                TableEnum::IsAdminAnswer->dbName()      =>  1,
                TableEnum::IsApproved->dbName()         =>  1,
            ]);
        } else {

            $answerCommnet->forceFill([
                TableEnum::UserId->dbName()             =>  auth()->user()->id,
                TableEnum::PostId->dbName()             =>  $comment->post->id,
                TableEnum::Comment->dbName()            =>  $answerText,
            ]);
        }

        $answerCommnet->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroyComment(Request $request): JsonResponse
    {
        if ($comment = Comment::find($request->input(TableEnum::Id->dbName()))) {

            // Delete comment likes
            $comment->likes()->delete();
            // Delete comment replies
            $comment->comments()->delete();

            $comment->delete();

            return JsonResponseHelper::successResponse(null, trans('general.ItemSuccessfullyRemoved'));
        }

        return JsonResponseHelper::errorResponse(null, trans('general.NotFoundItem'), HttpResponseStatusCode::BadRequest->value);
    }

}
