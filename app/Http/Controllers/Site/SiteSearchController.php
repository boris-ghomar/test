<?php

namespace App\Http\Controllers\Site;

use App\Enums\AccessControl\PostActionsEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Enums\Database\Tables\PostSpacesPermissionsTableEnum;
use App\Enums\Database\Tables\PostsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Settings\DynamicDataVariablesEnum;
use App\HHH_Library\general\php\Enums\SeoMetaTagsEnum;
use App\Http\Controllers\Controller;
use App\Models\BackOffice\PostGrouping\PostGroup;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\PostGrouping\PostSpacePermission;
use App\Models\BackOffice\Posts\Post;
use App\Models\BackOffice\Settings\DynamicData;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SiteSearchController extends Controller
{
    private $user;
    private $isPersonnel = false;
    private $clientCategory = null;

    private $beginTime = 0;

    public function index(Request $request, ?string $keyword = null)
    {
        $this->startTimer();
        $this->init();

        if (empty($keyword) && $request->has('keyword'))
            $keyword = $request->input('keyword');

        $paginator = empty($keyword) ? null : $this->getPostsCollection($keyword);

        $metaTags = [
            SeoMetaTagsEnum::MetaDescription->getHtmlTag(__('thisApp.AppName')),
            SeoMetaTagsEnum::MetaRobots->getHtmlTag('noindex, follow'),
            $this->getCanonicalUrl($request->url()),
        ];

        $searchResultInfo = __('thisApp.SearchResultInfo', [
            'resultCount'   => is_null($paginator) ? 0 : number_format($paginator->total(), 0),
            'time'  => $this->getElapsedTime(2)
        ]);

        $data = [
            'metaTags' => implode("\n", $metaTags),
            'keyword' => $keyword,
            'paginator' => $paginator,
            'searchResultInfo' => $searchResultInfo,
            'SearchGuideText' => DynamicData::get(DynamicDataVariablesEnum::Search_GuideText),
        ];

        return view('hhh.Site.pages.SearchPage.index', $data);
    }

    /**
     * Start timer
     *
     * @return float
     */
    private function startTimer(): float
    {
        return $this->beginTime = microtime(true);
    }

    /**
     * Get elapsed time
     *
     * @param  mixed $decimals (optional)
     * @param  mixed $startTime (optional)
     * @return string
     */
    private function getElapsedTime(int $decimals = 0, float $startTime = 0): string
    {
        $startTime = $startTime > 0 ? $startTime : $this->beginTime;
        $startTime = $startTime > 0 ? $startTime : microtime(true);

        return number_format(microtime(true) - $startTime, $decimals);
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
     * Get the canonical Url of post
     *
     * @param string $url
     * @return ?string
     */
    public function getCanonicalUrl(string $url): ?string
    {
        $appDomain = config('app.domain');
        $canonicalDomain = config('hhh_config.Domains.Canonical');

        if ($appDomain !== $canonicalDomain) {

            $canonicalUrl = Str::replaceFirst($appDomain, $canonicalDomain,  $url);
            return SeoMetaTagsEnum::Canonical->getHtmlTag($canonicalUrl);
        }

        return null;
    }

    /**
     * Get a collection of space posts
     *
     * @param  int $spaceId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function getPostsCollection(?string $keyword)
    {
        if (empty($keyword))
            return null;

        $exactlyKeyword = sprintf(' %s ', $keyword);
        $searchQuery = $this->getSearchQuery($exactlyKeyword);

        if (!$searchQuery->exists()) {
            $searchQuery = $this->getSearchQuery($keyword);
        }
        return $searchQuery->paginate(15);
    }

    /**
     * Get search query for keyword
     *
     * @param  string $keyword
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getSearchQuery(string $keyword): Builder
    {
        $idCol = PostsTableEnum::Id->dbName();
        $titleCol = PostsTableEnum::Title->dbName();
        $mainPhotoCol = PostsTableEnum::MainPhoto->dbName();
        $contentCol = PostsTableEnum::Content->dbName();
        $postSpaceIdCol = PostsTableEnum::PostSpaceId->dbName();
        $isPinnedCol = PostsTableEnum::IsPinned->dbName();

        $authorizedGroupIds = $this->getAuthorizedGroupIds();

        return Post::PublishedWithSort()
            ->whereIn($postSpaceIdCol, $authorizedGroupIds)
            ->where(function ($query) use ($contentCol, $titleCol, $keyword) {

                $query->where($titleCol, 'like', '%' . $keyword . '%')
                    ->orWhere($contentCol, 'like', '%' . $keyword . '%');
            })
            ->select($idCol, $titleCol, $mainPhotoCol, $contentCol, $postSpaceIdCol, $postSpaceIdCol, $isPinnedCol);
    }

    /**
     * Get authorized post group ids that user can see
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
