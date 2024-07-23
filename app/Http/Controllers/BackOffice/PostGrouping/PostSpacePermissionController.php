<?php

namespace App\Http\Controllers\BackOffice\PostGrouping;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\AccessControl\PostActionsEnum;
use App\Enums\Database\Tables\PostGroupsTableEnum;
use App\Enums\Database\Tables\PostSpacesPermissionsTableEnum as TableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\PostGrouping\PostSpacePermissionRequest;
use App\Http\Requests\traits\authorizeMethods;
use App\Http\Resources\BackOffice\PostGrouping\PostSpacePermissionCollection;
use App\Http\Resources\BackOffice\PostGrouping\PostSpacePermissionResource;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\Models\BackOffice\PostGrouping\PostSpace;
use App\Models\BackOffice\PostGrouping\PostSpacePermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostSpacePermissionController extends SuperJsGridController
{
    use authorizeMethods;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, PostSpacePermission::class);

        $this->updatePermissionsTable();

        $jsGrid_Controller = parent::getJsGridType(PostSpacePermission::class, jsGrid_Controller::jsGridType_Edit);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("post_grouping/post_space_permission");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'name'])));

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Id->dbName(), null);
        $fieldMaker->makeField_Text();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Hidden);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PostSpaceId->dbName(), __("thisApp.AdminPages.PostGrouping.PostSpaceId"));
        $options = DropdownListCreater::makeByModel(PostSpace::class, PostGroupsTableEnum::Title->dbName())
            ->useLable("name", "id")->prepend("", -1)->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, 300);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "start");
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::ClientCategoryId->dbName(), __("thisApp.AdminPages.PostGrouping.ClientCategoryId"));
        $options = DropdownListCreater::makeByModel(ClientCategory::class, RolesTableEnum::Name->dbName())
            ->useLable("name", "id")->prepend("", -1)->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "start");
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::PostAction->dbName(), __("thisApp.AdminPages.PostGrouping.PostAction"));
        $options = DropdownListCreater::makeByArray(PostActionsEnum::translatedArray())
            ->useLable("name", "key")->prepend("", "")->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, 150);
        $fieldMaker->setItemProperties($fieldMaker::field_Align, "start");
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::IsActive->dbName(), __('general.isActive'));
        $fieldMaker->makeField_Checkbox();
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(TableEnum::Descr->dbName(), __('general.Description'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.PostGrouping.PostSpacesPermissions.index', $data);
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
    public function show(PostSpacePermission $postSpacePermission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostSpacePermission $postSpacePermission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\BackOffice\PostGrouping\PostSpacePermissionRequest $request
     * @param  \App\Models\BackOffice\PostGrouping\PostSpacePermission $postSpacePermission
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(PostSpacePermissionRequest $request, PostSpacePermission $postSpacePermission): JsonResponse
    {
        try {

            $postSpacePermission = PostSpacePermission::find($request->input(TableEnum::Id->dbName()));

            $postSpacePermission->fill($request->all());
            $postSpacePermission->save();
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(),  HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new PostSpacePermissionResource($postSpacePermission), null, HttpResponseStatusCode::Accepted->value);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostSpacePermission $postSpacePermission)
    {
        //
    }

    /**
     * Return the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiIndex(Request $request)
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, PostSpacePermission::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new PostSpacePermissionCollection(
            PostSpacePermission::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }


    /**
     * This function updates the permissions table.
     *
     * In each call, the previously registered permissions
     * that have not changed remain unchanged,
     * so the user will not have any problems regarding previously permissions.
     *
     * Conclusion:
     * To change the permissions, just update the enum files
     * and run this page once to update the database.
     *
     * @return void
     */
    private function updatePermissionsTable(): void
    {

        $this->clearDeletedPermissions();
        $this->updatePermissions();
    }


    /**
     * Update permissions table in database
     *
     * @return void
     */
    private function updatePermissions(): void
    {
        $idKey = TableEnum::Id->dbName();
        $postSpaceIdKey = TableEnum::PostSpaceId->dbName();
        $clientCategoryIdKey = TableEnum::ClientCategoryId->dbName();
        $postActionKey = TableEnum::PostAction->dbName();

        $rolesTableIdKey = RolesTableEnum::Id->dbName();
        $postSpaceTableIdKey = PostGroupsTableEnum::Id->dbName();

        $clientCategories = ClientCategory::select($rolesTableIdKey)->orderBy($rolesTableIdKey, 'asc')->get()->pluck($rolesTableIdKey)->toArray();
        $postSpaces = PostSpace::select($postSpaceTableIdKey)->orderBy($postSpaceTableIdKey, 'asc')->get()->pluck($rolesTableIdKey)->toArray();
        $privatePostSpaces = PostSpace::PrivateSpaces()->select($postSpaceTableIdKey)->orderBy($postSpaceTableIdKey, 'asc')->get()->pluck($rolesTableIdKey)->toArray();
        $postActions = PostActionsEnum::names();

        foreach ($postSpaces as $postSpace) {

            foreach ($clientCategories as $clientCategory) {


                foreach ($postActions as $postAction) {

                    // Ignore View action for public spaces
                    if (in_array($postSpace, $privatePostSpaces) || $postAction !== PostActionsEnum::View->name) {

                        $postSpacePermission  = PostSpacePermission::where($postSpaceIdKey, $postSpace)
                            ->where($clientCategoryIdKey, $clientCategory)
                            ->where($postActionKey, $postAction)
                            ->exists();

                        if (!$postSpacePermission) {

                            $postSpacePermission = new PostSpacePermission();

                            $postSpacePermission->forceFill([

                                $idKey => Str::orderedUuid(),
                                $postSpaceIdKey => $postSpace,
                                $clientCategoryIdKey => $clientCategory,
                                $postActionKey => $postAction,
                            ]);

                            $postSpacePermission->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * Remove permissions that are not applicable
     *
     * @return void
     */
    private function clearDeletedPermissions(): void
    {
        $postSpaceIdKey = TableEnum::PostSpaceId->dbName();
        $clientCategoryIdKey = TableEnum::ClientCategoryId->dbName();
        $postActionKey = TableEnum::PostAction->dbName();

        // Delete permissions for non-existent post actions
        $notAvailablePostActions = PostSpacePermission::whereNotIn($postActionKey, PostActionsEnum::names());
        if ($notAvailablePostActions->count() > 0)
            $notAvailablePostActions->delete();

        // Delete permissions for non-existent post spaces
        $postSpacesIds = PostSpace::select(PostGroupsTableEnum::Id->dbName())->get()->pluck(PostGroupsTableEnum::Id->dbName())->toArray();
        $notAvailablePostSpacesPermissions = PostSpacePermission::whereNotIn($postSpaceIdKey, $postSpacesIds);
        if ($notAvailablePostSpacesPermissions->count() > 0)
            $notAvailablePostSpacesPermissions->delete();

        // Delete "View" permissions for public spaces
        $privatePostSpacesIds = PostSpace::PrivateSpaces()->select(PostGroupsTableEnum::Id->dbName())->get()->pluck(PostGroupsTableEnum::Id->dbName())->toArray();
        $notAvailablePostSpacesPermissions = PostSpacePermission::whereNotIn($postSpaceIdKey, $privatePostSpacesIds)->where($postActionKey, PostActionsEnum::View->name);
        if ($notAvailablePostSpacesPermissions->count() > 0)
            $notAvailablePostSpacesPermissions->delete();

        // Delete permissions for non-existent client categories
        $clientCategoryIds = ClientCategory::select(RolesTableEnum::Id->dbName())->get()->pluck(RolesTableEnum::Id->dbName())->toArray();
        $notAvailableClientCategoriesPermissions = PostSpacePermission::whereNotIn($clientCategoryIdKey, $clientCategoryIds);
        if ($notAvailableClientCategoriesPermissions->count() > 0)
            $notAvailableClientCategoriesPermissions->delete();
    }
}
