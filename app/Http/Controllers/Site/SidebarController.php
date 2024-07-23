<?php

namespace App\Http\Controllers\Site;

use App\Enums\AccessControl\PostActionsEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum as TableEnum;
use App\Enums\Database\Tables\PostSpacesPermissionsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Models\BackOffice\PostGrouping\PostGroup;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\PostGrouping\PostSpacePermission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SidebarController
{
    private $user = null;
    private $isPersonnel = false;
    private $clientCategory = null;
    private $rootGroups = [];
    private $authorizedGroupIds = [];

    function __construct()
    {
        $this->init();
    }

    /**
     * Get root groups
     *
     * @return array
     */
    public function getRootGroups(): array
    {
        return $this->rootGroups;
    }

    /**
     * Get subset groups of parent
     *
     * @param  int $parentId
     * @return array
     */
    public function getGroupSubsets(int $parentId): array
    {
        $idCol = TableEnum::Id->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();
        $isActiveCol = TableEnum::IsActive->dbName();
        $isSpaceCol = TableEnum::IsSpace->dbName();
        $titleCol = TableEnum::Title->dbName();

        $subsets = PostGroup::where($parentIdCol, $parentId)
            ->where($isActiveCol, 1)
            ->whereIn($idCol, $this->authorizedGroupIds)
            ->select($idCol, $parentIdCol, $titleCol, $isSpaceCol)
            ->orderBy(TableEnum::Position->dbName(), 'asc')
            ->get();

        $availableSubsets = [];

        foreach ($subsets as $subset) {

            $subset['display_url'] = $subset->DisplayUrl;
            array_push($availableSubsets, $subset->toArray());
        }

        return $availableSubsets;
    }


    /**
     * init
     *
     * @return void
     */
    private function init(): void
    {

        if (Auth::check()) {
            $clientCategoryIdCol = RolesTableEnum::Id->dbName();
            $clientCategoryIsActiveCol = RolesTableEnum::IsActive->dbName();

            $this->user = User::authUser();
            $this->isPersonnel = $this->user->isPersonnel();

            if (!$this->isPersonnel)
                $this->clientCategory = $this->user->role()->select($clientCategoryIdCol, $clientCategoryIsActiveCol)->first();
        }

        $this->setPresentableSapces();
    }




    /**
     * Set Presentable Sapces and root group IDs
     *
     *
     * @return void
     */
    private function setPresentableSapces(): void
    {
        $idCol = TableEnum::Id->dbName();
        $parentIdCol = TableEnum::ParentId->dbName();
        $isActiveCol = TableEnum::IsActive->dbName();
        $titleCol = TableEnum::Title->dbName();
        $isPublicSpaceCol = TableEnum::IsPublicSpace->dbName();

        $activeSapces = PostSpace::has('publishedPosts')
            ->where($isActiveCol, 1)
            ->select($idCol, $parentIdCol, $titleCol, $isPublicSpaceCol)
            ->get();

        $presentableSapcesForUser = new Collection();
        $rootGroupIds = [];
        $authorizedGroupIds = [];

        foreach ($activeSapces as $space) {

            $hasPresentablePotential = false;

            if ($space->$isPublicSpaceCol)
                $hasPresentablePotential = true;
            else if ($this->isPersonnel)
                $hasPresentablePotential = true;
            else if ($this->canDisplaySpaceToClient($space))
                $hasPresentablePotential = true;

            if ($hasPresentablePotential) {

                $rootParent = $this->getRootParent($space);
                if (!is_null($rootParent)) {

                    $presentableSapcesForUser->add($space);

                    $rootParentId = $rootParent->$idCol;
                    if (!in_array($rootParentId, $rootGroupIds))
                        array_push($rootGroupIds, $rootParent->$idCol);

                    $authorizedGroupIds = array_merge($authorizedGroupIds, $this->getGroupRoute($space));
                }
            }
        }

        $this->authorizedGroupIds = array_unique($authorizedGroupIds);

        $this->rootGroups = PostGroup::whereIn($idCol, $rootGroupIds)
            ->select($idCol, $titleCol)
            ->orderBy(TableEnum::Position->dbName(), 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Check can display space to client
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostSpace $space
     * @return bool
     */
    private function canDisplaySpaceToClient(PostSpace $space): bool
    {
        // Geust user only can see the public posts
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
        $postSpacePermission = PostSpacePermission::where($permissionPostSpaceIdCol, $space->$spaceIdCol)
            ->where($permissionClientCategoryIdCol, $clientCategory->$clientCategoryIdCol)
            ->where($permissionPostActionCol, PostActionsEnum::View->name)
            ->where($permissionIsActiveCol, 1);

        if (!$postSpacePermission->exists())
            return false;

        // Checks whether the parents of this space are active or not
        $rootParent = $this->getRootParent($space);
        if (is_null($rootParent))
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
