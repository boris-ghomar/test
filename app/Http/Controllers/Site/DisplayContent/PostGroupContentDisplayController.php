<?php

namespace App\Http\Controllers\Site\DisplayContent;

use App\Enums\AccessControl\PostActionsEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Enums\Database\Tables\PostSpacesPermissionsTableEnum;
use App\Enums\Database\Tables\PostsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\Enums\SeoMetaTagsEnum;
use App\Http\Controllers\Controller;
use App\Models\BackOffice\PostGrouping\PostGroup;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\PostGrouping\PostSpacePermission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PostGroupContentDisplayController extends Controller
{
    private $user;
    private $isPersonnel = false;
    private $clientCategory = null;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostGroup $postGroup
     * @param  ?string $slug
     * @return \Illuminate\Contracts\View\View|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function show(PostGroup $postGroup, ?string $slug)
    {
        if ($slug !== $postGroup->UrlSlug)
            return redirect($postGroup->DisplayUrl, 301);

        $this->init();

        if (!$this->canDisplayContent($postGroup)) {

            $statusCode = HttpResponseStatusCode::Forbidden->value;

            $data = [
                'statusCode' => $statusCode,
                'statusMessage' => HttpResponseStatusCode::getMessageByCode($statusCode),
                'messages' => [__('thisApp.Errors.Forbidden')],
            ];
            return response(view('hhh.Site.super_error', $data), $statusCode);
        }

        return $postGroup[TableEnum::IsSpace->dbName()] ? $this->showSpace($postGroup) : $this->showCategory($postGroup);
    }

    /**
     * Display the post space resource.
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostGroup $postGroup
     * @return \Illuminate\Contracts\View\View|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function showSpace(PostGroup $postGroup)
    {
        $idCol = TableEnum::Id->dbName();
        $titleCol = TableEnum::Title->dbName();
        $descriptionCol = TableEnum::Description->dbName();
        $templateCol = TableEnum::Template->dbName();
        $canonicalUrlCol = TableEnum::CanonicalUrl->dbName();

        $paginator = $this->getSpacePostsCollection($postGroup->$idCol);

        $metaTags = [
            SeoMetaTagsEnum::MetaDescription->getHtmlTag($postGroup->$descriptionCol),
            SeoMetaTagsEnum::MetaRobots->getHtmlTag(),
            $postGroup->$canonicalUrlCol,
        ];

        $data = [
            'pageTitle' => $postGroup->$titleCol,
            'metaTags' => implode("\n", $metaTags),
            'collection' => null,
            'paginator' => $paginator,
        ];

        return view('hhh.Site.pages.PostSpaces.' . $postGroup->$templateCol . '.index', $data);
    }

    /**
     * Display the post category resource.
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostGroup $postGroup
     * @return \Illuminate\Contracts\View\View|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function showCategory(PostGroup $postGroup)
    {
        $idCol = TableEnum::Id->dbName();
        $titleCol = TableEnum::Title->dbName();
        $descriptionCol = TableEnum::Description->dbName();
        $canonicalUrlCol = TableEnum::CanonicalUrl->dbName();

        $paginator = $this->getGroupSubsets($postGroup->$idCol);
        // dd($paginator);
        $metaTags = [
            SeoMetaTagsEnum::MetaDescription->getHtmlTag($postGroup->$descriptionCol),
            SeoMetaTagsEnum::MetaRobots->getHtmlTag(),
            $postGroup->$canonicalUrlCol,
        ];

        $data = [
            'pageTitle' => $postGroup->$titleCol,
            'metaTags' => implode("\n", $metaTags),
            'paginator' => $paginator,
        ];

        return view('hhh.Site.pages.LinkList.index', $data);
    }

    /**
     * Init dispaly content
     *
     * @return void
     */
    private function init()
    {

        if (Auth::check()) {

            $clientCategoryIdCol = RolesTableEnum::Id->dbName();
            $clientCategoryIsActiveCol = RolesTableEnum::IsActive->dbName();

            $this->user = User::authUser();
            $this->isPersonnel = $this->user->isPersonnel();

            if (!$this->isPersonnel)
                $this->clientCategory = $this->user->role()->select($clientCategoryIdCol, $clientCategoryIsActiveCol)->first();
        }
    }

    /**
     * Check if you can display the content of this group to the client
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostSpace $space
     * @return bool
     */
    private function canDisplayContent(PostGroup $postGroup): bool
    {
        $isPublicSpaceCol = TableEnum::IsPublicSpace->dbName();
        $isActiveCol = TableEnum::IsActive->dbName();

        if (!$postGroup->$isActiveCol)
            return false;

        $hasPresentablePotential = false;

        if ($postGroup->$isPublicSpaceCol)
            $hasPresentablePotential = true;
        else if ($this->isPersonnel)
            $hasPresentablePotential = true;
        else if ($this->clientHasAccessPermissions($postGroup))
            $hasPresentablePotential = true;

        if (!$hasPresentablePotential)
            return false;

        // Checks whether the parents of this space are active or not
        $rootParent = $this->getRootParent($postGroup);
        if (is_null($rootParent))
            return false;

        return true;
    }

    /**
     * Check if client has access permissions
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostGroup $postGroup
     * @return bool
     */
    private function clientHasAccessPermissions(PostGroup $postGroup): bool
    {
        // Pass the group category
        if (!$postGroup[TableEnum::IsSpace->dbName()])
            return true;

        // Geust user only can see the public post spaces
        if (is_null($this->user))
            return false;

        $clientCategoryIdCol = RolesTableEnum::Id->dbName();
        $clientCategoryIsActiveCol = RolesTableEnum::IsActive->dbName();

        $clientCategory = $this->clientCategory;

        // Client category is not active
        if (!$clientCategory->$clientCategoryIsActiveCol)
            return false;


        $spaceIdCol = TableEnum::Id->dbName();
        $permissionPostSpaceIdCol = PostSpacesPermissionsTableEnum::PostSpaceId->dbName();
        $permissionClientCategoryIdCol = PostSpacesPermissionsTableEnum::ClientCategoryId->dbName();
        $permissionPostActionCol = PostSpacesPermissionsTableEnum::PostAction->dbName();
        $permissionIsActiveCol = PostSpacesPermissionsTableEnum::IsActive->dbName();

        // Can client view space (parents not included, it's just space permissions)
        $postSpacePermission = PostSpacePermission::where($permissionPostSpaceIdCol, $postGroup->$spaceIdCol)
            ->where($permissionClientCategoryIdCol, $clientCategory->$clientCategoryIdCol)
            ->where($permissionPostActionCol, PostActionsEnum::View->name)
            ->where($permissionIsActiveCol, 1);

        if (!$postSpacePermission->exists())
            return false;


        return true;
    }

    /**
     * Get root parent
     * (Zero-level parent whose parent ID is 0)
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostGroup $postGroup
     * @param  bool $groupsMustBeActive
     * @return \App\Models\BackOffice\PostGrouping\PostGroup|null (If it is a root parent, it returns itself)
     */
    private function getRootParent(PostGroup $postGroup, bool $groupsMustBeActive = true): ?PostGroup
    {
        $idCol = TableEnum::Id->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();
        $titleCol = TableEnum::Title->dbName();
        $isActiveCol = TableEnum::IsActive->dbName();

        if ($postGroup->$parentIdCol === 0)
            return $postGroup; //finish

        $parent = $postGroup->parentGroup()
            ->select($idCol, $parentIdCol, $titleCol);

        if ($groupsMustBeActive)
            $parent = $parent->where($isActiveCol, 1);

        $parent = $parent->first();

        if (is_null($parent))
            return null;

        $parentOfParent = $this->getRootParent($parent, $groupsMustBeActive);

        return is_null($parentOfParent) ? null : $parentOfParent;
    }

    /**
     * Get subset groups of parent
     *
     * @param  int $parentId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function getGroupSubsets(int $parentId)
    {
        $idCol = TableEnum::Id->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();
        $isActiveCol = TableEnum::IsActive->dbName();
        $isSpaceCol = TableEnum::IsSpace->dbName();
        $titleCol = TableEnum::Title->dbName();
        $descriptionCol = TableEnum::Description->dbName();

        $authorizedGroupIds = $this->getAuthorizedGroupIds();

        $subsets = PostGroup::where($parentIdCol, $parentId)
            ->where($isActiveCol, 1)
            ->whereIn($idCol, $authorizedGroupIds)
            // ->select($idCol, $parentIdCol, $titleCol, $isSpaceCol, $descriptionCol)
            ->orderBy(TableEnum::Position->dbName(), 'asc')
            ->paginate(15);

        return $subsets;

        /* $availableSubsets = [];

        foreach ($subsets as $subset) {

            $subset['url'] = $subset->DisplayUrl;
            array_push($availableSubsets, $subset->toArray());
        }

        return $availableSubsets; */
    }

    /**
     * Get a collection of space posts
     *
     * @param  int $spaceId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function getSpacePostsCollection(int $spaceId)
    {
        $idCol = PostsTableEnum::Id->dbName();
        $titleCol = PostsTableEnum::Title->dbName();
        $mainPhotoCol = PostsTableEnum::MainPhoto->dbName();
        $contentCol = PostsTableEnum::Content->dbName();
        $contentCol = PostsTableEnum::Content->dbName();
        $postSpaceIdCol = PostsTableEnum::PostSpaceId->dbName();
        $isPinnedCol = PostsTableEnum::IsPinned->dbName();

        $space = PostSpace::find($spaceId);

        $posts = $space->publishedPostsWithSort()
            ->select($idCol, $titleCol, $mainPhotoCol, $contentCol, $postSpaceIdCol, $isPinnedCol)
            ->paginate(15);

        return $posts;
    }

    /**
     * Get authorized group ids that user can see
     *
     *
     * @return array
     */
    private function getAuthorizedGroupIds(): array
    {
        $idCol = TableEnum::Id->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();
        $isActiveCol = TableEnum::IsActive->dbName();
        $titleCol = TableEnum::Title->dbName();
        $isPublicSpaceCol = TableEnum::IsPublicSpace->dbName();

        $activeSapces = PostSpace::has('publishedPosts')
            ->where($isActiveCol, 1)
            // ->select($idCol, $parentIdCol, $titleCol, $isPublicSpaceCol, $isActiveCol)
            ->get();

        $rootGroupIds = [];
        $authorizedGroupIds = [];

        foreach ($activeSapces as $space) {

            $hasPresentablePotential = false;

            if ($space->$isPublicSpaceCol)
                $hasPresentablePotential = true;
            else if ($this->isPersonnel)
                $hasPresentablePotential = true;
            else if ($this->canDisplayContent($space))
                $hasPresentablePotential = true;

            if ($hasPresentablePotential) {

                $rootParent = $this->getRootParent($space);
                if (!is_null($rootParent)) {

                    $rootParentId = $rootParent->$idCol;
                    if (!in_array($rootParentId, $rootGroupIds))
                        array_push($rootGroupIds, $rootParent->$idCol);

                    $authorizedGroupIds = array_merge($authorizedGroupIds, $this->getGroupRoute($space));
                }
            }
        }

        return array_unique($authorizedGroupIds);
    }

    /**
     * Get the groups that exist on the path to reach the root group
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostGroup $postGroup
     * @param  array $route Do not fill this variable, this is a callback function and will use this variable when it returns
     * @return array
     */
    private function getGroupRoute(PostGroup $postGroup, array $route = []): array
    {
        $idCol = TableEnum::Id->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();
        $titleCol = TableEnum::Title->dbName();

        if (!in_array($postGroup->$idCol, $route))
            array_push($route, $postGroup->$idCol);

        if ($postGroup->$parentIdCol === 0)
            return $route; // finish

        $parent = $postGroup->parentGroup()
            ->select($idCol, $parentIdCol, $titleCol)
            ->first();

        if (is_null($parent))
            return [];

        return $this->getGroupRoute($parent, $route);
    }
}
