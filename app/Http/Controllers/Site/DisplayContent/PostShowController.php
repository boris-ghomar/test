<?php

namespace App\Http\Controllers\Site\DisplayContent;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\AccessControl\PostActionsEnum;
use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\CommentsTableEnum;
use App\Enums\Database\Tables\LikesTableEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\Enums\Database\Tables\PostSpacesPermissionsTableEnum;
use App\Enums\Database\Tables\PostsTableEnum as TableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\UserActions\CommentableTypesEnum;
use App\Enums\UserActions\LikableTypesEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\Enums\SeoMetaTagsEnum;
use App\HHH_Library\general\php\traits\AddAttributesPad;
use App\HHH_Library\QuillEditor\QuillEditorHelper;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Models\BackOffice\PostGrouping\PostGroup;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\PostGrouping\PostSpacePermission;
use App\Models\BackOffice\Posts\ArticlePost;
use App\Models\BackOffice\Posts\FaqPost;
use App\Models\BackOffice\Posts\Post;
use App\Models\Site\UserActions\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class PostShowController extends SuperController
{
    use AddAttributesPad;

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BackOffice\Posts\ArticlePost $articlePost
     * @param  ?string $slug
     * @return \Illuminate\Contracts\View\View|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function showArticle(ArticlePost $articlePost, ?string $slug)
    {
        if ($slug !== $articlePost->UrlSlug)
            return redirect($articlePost->DisplayUrl, 301);

        if (!$this->canUserViewPost($articlePost))
            return $this->accessDenied();

        $postId = $articlePost->getAttribute(TableEnum::Id->dbName());
        $editorWidget = QuillEditorHelper::setContentViaFile($postId);

        $data = [
            "canEditPost"   => $this->canUserEditPost($articlePost),
            "editLink"      => $articlePost->EditUrl,
            "post"          => $articlePost,
            "TabelEnum"     => TableEnum::class,
            "htmlContent"   => $editorWidget->getHtml(),
            "autorMeta"     => SeoMetaTagsEnum::Author->getHtmlTag(config('app.name') . " Content Team"),
            "postActions"   => $this->getActionableActions($articlePost),
            "postComments"  => $this->getCommentableComments($articlePost),
        ];

        $articlePost->increment(TableEnum::Views->dbName());

        return view('hhh.Site.pages.PostShow.Article.index', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BackOffice\Posts\FaqPost $faqPost
     * @param  ?string $slug
     * @return \Illuminate\Contracts\View\View|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function showFaq(FaqPost $faqPost, ?string $slug)
    {
        if ($slug !== $faqPost->UrlSlug)
            return redirect($faqPost->DisplayUrl, 301);

        if (!$this->canUserViewPost($faqPost))
            return $this->accessDenied();

        $metaTags = [
            SeoMetaTagsEnum::MetaDescription->getHtmlTag($faqPost[TableEnum::MetaDescription->dbName()]),
            SeoMetaTagsEnum::MetaRobots->getHtmlTag(),
            $faqPost->CanonicalUrl,
            SeoMetaTagsEnum::Author->getHtmlTag(config('app.name') . " Content Team"),
        ];

        $contentKey = TableEnum::Content->dbName();
        $faqPost[$contentKey] = str_replace("\n", "<br/>", $faqPost[$contentKey]);


        $data = [
            "canEditPost"   => $this->canUserEditPost($faqPost),
            "editLink"      => $faqPost->EditUrl,
            "post"          => $faqPost,
            "TabelEnum"     => TableEnum::class,
            "metaTags"      => implode("\n", $metaTags),
            "postActions"   => $this->getActionableActions($faqPost),
            "postComments"  => $this->getCommentableComments($faqPost),
        ];

        $faqPost->increment(TableEnum::Views->dbName());

        return view('hhh.Site.pages.PostShow.FAQ.index', $data);
    }

    /**
     * Check whether that auth user can edit post
     *
     * @param \App\Models\BackOffice\Posts\Post $post
     * @return bool
     */
    private function canUserEditPost(Post $post): bool
    {
        if (auth()->check()) {

            $user = User::authUser();

            if ($user->isPersonnel()) {

                return $user->can(PermissionAbilityEnum::update->name, $post);
            }
        }
        return false;
    }

    /**
     * Redirect the unauthorized user to the forbidden page
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    private function accessDenied()
    {
        if (!auth()->check())
            return redirect(SitePublicRoutesEnum::defaultLogin()->route());

        $statusCode = HttpResponseStatusCode::Forbidden->value;

        return response(view('hhh.Site.pages.PostShow.PrivatePostsError.index'), $statusCode);
    }

    /**
     * Can user view the post
     *
     * @param  \App\Models\BackOffice\Posts\Post $post
     * @return bool
     */
    private function canUserViewPost(Post $post): bool
    {
        $postSpace = $post->postSpace;
        $user = User::authUser();

        if (!is_null($user)) {
            // Logged user

            // Maybe personnel want to preview post before publish
            if ($user->isPersonnel())
                return true;

            // Clients
            if (!$post[TableEnum::IsPublished->dbName()])
                return false;

            return $this->clientHasAccessToSpace($postSpace) ? $this->isParentsActive($postSpace) : false;
        } else {
            // Geust user
            if (!$post[TableEnum::IsPublished->dbName()])
                return false;

            $isPublicSpace = $postSpace[PostGroupsTableEnum::IsPublicSpace->dbName()];

            return $isPublicSpace ? $this->isParentsActive($postSpace) : false;
        }
    }

    /**
     * Check if the parent of the post is active
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostSpace $postSpace
     * @return bool
     */
    private function isParentsActive(PostSpace $postSpace): bool
    {
        $isActiveCol = PostGroupsTableEnum::IsActive->dbName();

        if (!$postSpace->$isActiveCol)
            return false;

        return is_null($this->getRootParent($postSpace)) ? false : true;
    }

    /**
     * Get root parent
     * (Zero-level parent whose parent ID is 0)
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostGroup $postGroup
     * @param  bool $groupsMustBeActive
     * @return \App\Models\BackOffice\PostGrouping\PostGroup|null (If it is a root parent, it returns itself)
     */
    private function getRootParent(PostGroup $postGroup): ?PostGroup
    {
        $idCol = PostGroupsTableEnum::Id->dbName();
        $parentIdCol = PostGroupsTableEnum::ParentId->dbName();
        $titleCol = PostGroupsTableEnum::Title->dbName();
        $isActiveCol = PostGroupsTableEnum::IsActive->dbName();

        if ($postGroup->$parentIdCol === 0)
            return $postGroup; //finish

        $parent = $postGroup->parentGroup()
            ->select($idCol, $parentIdCol, $titleCol)
            ->where($isActiveCol, 1)
            ->first();

        if (is_null($parent))
            return null;

        $parentOfParent = $this->getRootParent($parent);

        return is_null($parentOfParent) ? null : $parentOfParent;
    }

    /**
     * Check if client has access permissions to post space
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostSpace $postSpace
     * @return bool
     */
    private function clientHasAccessToSpace(PostSpace $postSpace): bool
    {
        if (!$postSpace[PostGroupsTableEnum::IsActive->dbName()])
            return false;

        if ($postSpace[PostGroupsTableEnum::IsPublicSpace->dbName()])
            return true;

        $user = User::authUser();

        // Geust user only can see the public post spaces
        if (is_null($user))
            return false;

        $clientCategoryIdCol = RolesTableEnum::Id->dbName();
        $clientCategoryIsActiveCol = RolesTableEnum::IsActive->dbName();

        $clientCategory = $user->role()->select($clientCategoryIdCol, $clientCategoryIsActiveCol)->first();

        // Client category is not active
        if (!$clientCategory->$clientCategoryIsActiveCol)
            return false;


        $spaceIdCol = TableEnum::Id->dbName();
        $permissionPostSpaceIdCol = PostSpacesPermissionsTableEnum::PostSpaceId->dbName();
        $permissionClientCategoryIdCol = PostSpacesPermissionsTableEnum::ClientCategoryId->dbName();
        $permissionPostActionCol = PostSpacesPermissionsTableEnum::PostAction->dbName();
        $permissionIsActiveCol = PostSpacesPermissionsTableEnum::IsActive->dbName();

        // Can client view space (parents not included, it's just space permissions)
        $postSpacePermission = PostSpacePermission::where($permissionPostSpaceIdCol, $postSpace->$spaceIdCol)
            ->where($permissionClientCategoryIdCol, $clientCategory->$clientCategoryIdCol)
            ->where($permissionPostActionCol, PostActionsEnum::View->name)
            ->where($permissionIsActiveCol, 1);

        if ($postSpacePermission->exists())
            return true;


        return false;
    }

    /**
     * Get actions data of Actionable (Post|Comment)
     *
     * @param  \App\Models\BackOffice\Posts\Post|App\Models\Site\UserActions\Comment $actionable
     * @return array
     */
    private function getActionableActions(Post|Comment $actionable): array
    {
        return [
            'like'      => $this->getLikableLikeAction($actionable),
            'comment'   => $this->getCommentableCommentAction($actionable),
        ];
    }

    /**
     * Get like action data of Likable (Post Or Comment)
     *
     * @param  \App\Models\BackOffice\Posts\Post|App\Models\Site\UserActions\Comment $likable
     * @return array
     */
    private function getLikableLikeAction(Post|Comment $likable): array
    {
        $likableType = null;
        if ($likable instanceof Post)
            $likableType = LikableTypesEnum::Post->name;
        else if ($likable instanceof Comment)
            $likableType = LikableTypesEnum::Comment->name;
        else
            return []; // Unknown likable instance

        $user = User::authUser();

        if (!is_null($user)) {

            $userId = $user[UsersTableEnum::Id->dbName()];

            $isUserLiked = $likable->likes()
                ->where(LikesTableEnum::UserId->dbName(), $userId)
                ->exists();
        } else {
            $isUserLiked = false;
            $userId = -1;
        }

        $data = [
            'likableId'     => $likable[TableEnum::Id->dbName()],
            'likableType'   => $likableType,
            'userId'        => $userId, // Added for generate unique key per user
        ];
        $likableViewId = sprintf("like_%s_%s", $data['likableId'], $data['likableType']);

        $encryptedData = Crypt::encryptString(json_encode($data));

        return [
            'isUserLiked'   => $isUserLiked,
            'count'         => number_format($likable->likes()->count()),
            'likableViewId' => $likableViewId,
            'onSubmit'      => sprintf("postAction.like('%s','%s');", $likableViewId, $encryptedData), // the "postAction" class name definded in "_user_actions_js.blade"
        ];
    }

    /**
     * Get comment action data of Commentable (Post or Comment)
     *
     * @param  \App\Models\BackOffice\Posts\Post|App\Models\Site\UserActions\Comment $Commentable
     * @return array
     */
    private function getCommentableCommentAction(Post|Comment $commentable): array
    {
        $commentableType = null;
        if ($commentable instanceof Post) {
            $commentableType = CommentableTypesEnum::Post->name;
            $postId = $commentable->id;
        } else if ($commentable instanceof Comment) {
            $commentableType = CommentableTypesEnum::Comment->name;
            $postId = $commentable->post->id;
        } else
            return []; // Unknown commentable instance

        $user = User::authUser();
        $userId = is_null($user) ? -1 : $user[UsersTableEnum::Id->dbName()];

        $data = [
            'commentableId'     => $commentable[TableEnum::Id->dbName()],
            'commentableType'   => $commentableType,
            'postId'            => $postId,
            'userId'            => $userId, // Added for generate unique key per user
        ];

        $commentableViewId = sprintf("comment_%s_%s", $data['commentableId'], $data['commentableType']);

        $encryptedData = Crypt::encryptString(json_encode($data));

        return [
            'count'             => number_format($commentable->comments()->Approved()->count()),
            'commentableViewId' => $commentableViewId,
            'onSubmit'          => sprintf("postAction.comment('%s','%s');", $commentableViewId, $encryptedData), // the "postAction" class name definded in "_user_actions_js.blade"s
        ];
    }

    /**
     * Get comments of Commentable (Post or Comment)
     *
     * @param  \App\Models\BackOffice\Posts\Post|App\Models\Site\UserActions\Comment $Commentable
     * @return array
     */
    private function getCommentableComments(Post|Comment $commentable): array
    {
        $commentableComments = $commentable->comments()->approved()
            ->orderBy(TimestampsEnum::CreatedAt->dbName(), 'asc')
            ->get();

        $comments = [];
        foreach ($commentableComments as $comment) {

            // Reply limited to only post comment and closed for replies
            if ($comment[CommentsTableEnum::CommentableType->dbName()] == CommentableTypesEnum::Post->name)
                $actions = $this->getActionableActions($comment);
            else
                $actions = ['like' => $this->getLikableLikeAction($comment)];

            $user = $comment->user;

            $commentResource = [
                'data' => [
                    'text'          => $comment[CommentsTableEnum::Comment->dbName()],
                    'htmlViewId'    => $comment->HtmlViewId,
                    'profilePhoto'  => $user[UsersTableEnum::PhotoUrl->dbName()],
                    'displayName'   => $comment->OwnerDispalyName,

                    'replies' => $this->getCommentableComments($comment),
                ],
                'actions' => $actions,
            ];

            array_push($comments, $commentResource);
        }
        return $comments;
    }
}
