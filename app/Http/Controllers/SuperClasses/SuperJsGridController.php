<?php

namespace App\Http\Controllers\SuperClasses;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\CustomizedPagesTableEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonHelper;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_Controller;
use App\Models\General\CustomizedPage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class SuperJsGridController extends SuperController
{

    /************************ implements *******************************/
    /**
     * Return the specified resource from storage.
     *
     * This function is used in JavaScript API
     * when requesting the first table page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    abstract function apiIndex(Request $request);

    /************************ implements END *******************************/

    /**
     * Get an instance of jsGrid based on the permission granted to the user
     *
     * @param  string $modelClass
     * @param  int $maxAbility : Maximum ability of jsGrid Table
     * @return \App\HHH_Library\jsGrid\jsGrid_Controller | null
     */
    protected static function getJsGridType(string $modelClass, int $maxAbility = jsGrid_Controller::jsGridType_Validation): jsGrid_Controller | null
    {

        /** @var User $user */
        $user = auth()->user();

        $jsGridType = null;

        if ($user->isSuperAdmin()) {

            // It prevents Super Admin from granting items that are not applicable in the table.
            $jsGridType = $maxAbility;
        } else {

            $canViewAny = $user->can(PermissionAbilityEnum::viewAny->name, $modelClass);
            $canCreate = $user->can(PermissionAbilityEnum::create->name, $modelClass);
            $canUpdate = $user->can(PermissionAbilityEnum::update->name, $modelClass) || $user->can(PermissionAbilityEnum::restore->name, $modelClass);
            $canDelete = $user->can(PermissionAbilityEnum::delete->name, $modelClass) || $user->can(PermissionAbilityEnum::forceDelete->name, $modelClass);


            if ($canViewAny)
                $jsGridType = jsGrid_Controller::jsGridType_Filter;

            if ($canCreate)
                $jsGridType = jsGrid_Controller::jsGridType_Insert;

            if ($canUpdate)
                $jsGridType = jsGrid_Controller::jsGridType_Edit;

            if ($canDelete)
                $jsGridType = jsGrid_Controller::jsGridType_Delete;

            if ($canCreate && $canUpdate)
                $jsGridType = jsGrid_Controller::jsGridType_InsertEdit;

            if ($canCreate && $canDelete)
                $jsGridType = jsGrid_Controller::jsGridType_InsertDelete;

            if ($canUpdate && $canDelete)
                $jsGridType = jsGrid_Controller::jsGridType_EditDelete;

            if ($canCreate && $canUpdate && $canDelete)
                $jsGridType = jsGrid_Controller::jsGridType_Validation;
        }

        return is_null($jsGridType) ? null : (new jsGrid_Controller("jsGrid", $jsGridType));
    }

    /**
     * Update customize table settings
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCustomizeTableSettings(Request $request): JsonResponse
    {
        try {
            $selectedColumnsKey = "customizablePageSelectedColumns";

            if ($request->has($selectedColumnsKey)) {

                $selectedColumns = $request->input($selectedColumnsKey);

                if (JsonHelper::isJson($selectedColumns)) {

                    $route = $this->customizablePageRoute(); // Definded in the extended class

                    $user = User::authUser();
                    $customizedPage = $user->customizedPage($route);

                    if (is_null($customizedPage)) {
                        $customizedPage = new CustomizedPage([
                            CustomizedPagesTableEnum::Route->dbName() => $route->value,
                            CustomizedPagesTableEnum::UserId->dbName() => $user->id,
                        ]);
                    }

                    $customizedPage[CustomizedPagesTableEnum::SelectedColumns->dbName()] = $selectedColumns;
                    $customizedPage->save();

                    return JsonResponseHelper::successResponse(null, trans('general.DataSavedSuccessfully'));
                }
            }
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'), HttpResponseStatusCode::BadRequest->value);
    }

    /**
     * Get customizable page settings
     *
     * @return array
     */
    public function getCustomizablePageSettings(): array
    {
        $route = $this->customizablePageRoute(); // Definded in the extended class
        $customizedPage = User::authUser()->customizedPage($route);

        $selectableColumns = $this->getCustomizablePageSelectableColumns();
        $requiredColumns = $this->getCustomizablePageRequiredColumns();
        $selectedColumns = is_null($customizedPage) ? array_keys($selectableColumns) : $customizedPage[CustomizedPagesTableEnum::SelectedColumns->dbName()];

        return [
            config('hhh_config.keywords.requiredColumns')   => $requiredColumns,
            config('hhh_config.keywords.selectableColumns') => $selectableColumns,
            config('hhh_config.keywords.selectedColumns')   => $selectedColumns,
            config('hhh_config.keywords.displayColumns')    => array_merge(array_keys($requiredColumns), $selectedColumns),
        ];
    }
}
