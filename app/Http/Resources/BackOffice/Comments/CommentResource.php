<?php

namespace App\Http\Resources\BackOffice\Comments;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\CommentsTableEnum as TableEnum;
use App\Enums\Database\Tables\PostsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\Http\Resources\ApiResponseResource;
use App\Models\BackOffice\Comments\Comment;
use App\Models\User;

class CommentResource extends ApiResponseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $commentIdCol = TableEnum::Id->dbName();
        $postIdCol = TableEnum::PostId->dbName();
        $userIdCol = TableEnum::UserId->dbName();
        $commentCol = TableEnum::Comment->dbName();
        $isApprovedCol = TableEnum::IsApproved->dbName();
        $approvedByCol = TableEnum::ApprovedBy->dbName();

        $createdAtCol = TimestampsEnum::CreatedAt->dbName();
        $updatedAtCol = TimestampsEnum::UpdatedAt->dbName();

        if (is_null($this->post())) {
            $this->delete();
            return [];
        }

        $post = $this->post;
        $commentLink = sprintf(
            '<a target="_blank" href="%s#%s_anchor_link">%s</a>',
            $post[PostsTableEnum::DisplayUrl->dbName()],
            $this->HtmlViewId,
            $post[PostsTableEnum::Title->dbName()],
        );

        $username = "";
        /** @var User $user  */
        $user = $this->user;
        if ($user->isPersonnel()) {

            $username = is_null($this->owner_username) ? $user[UsersTableEnum::Username->dbName()] : $this->owner_username;
        } else if ($user->isClient()) {

            if (is_null($this->client_username)) {

                $userExtra = $user->userExtra;

                $username = is_null($userExtra) ? "" : $userExtra[ClientModelEnum::Login->dbName()];
            } else {
                $username = $this->client_username;
            }
        }

        $answer = $this->adminAnswer;

        if (empty($this->$postIdCol)) {

            $this->$postIdCol = $post->id;
            $comment = Comment::find($this->$commentIdCol);
            $comment->$postIdCol = $this->$postIdCol;
            $comment->save();
        }

        return [
            $commentIdCol   => (int) $this[$commentIdCol],
            $postIdCol      => (int) $this->$postIdCol,
            $userIdCol      => (int) $this[$userIdCol],
            $commentCol     => $this[$commentCol],
            $isApprovedCol  => $this[$isApprovedCol],
            $createdAtCol   => $this[$createdAtCol],
            $approvedByCol  => (int) $this[$approvedByCol],
            $updatedAtCol   => $this[$updatedAtCol],

            'OwnerUsername' => $username,
            'CommentLink'   => $commentLink,
            'displayName'   => $user->DisplayName,
            'answer'        => is_null($answer) ? null : $answer->$commentCol,
        ];
    }
}
