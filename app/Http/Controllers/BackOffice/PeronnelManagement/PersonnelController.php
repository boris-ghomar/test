<?php

namespace App\Http\Controllers\BackOffice\PeronnelManagement;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\PersonnelExtrasTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Routes\AdminRoutesEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Enums\Users\UsersTypesEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\GendersEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\jsGrid\jsGrid_FieldMaker;
use App\Http\Controllers\SuperClasses\SuperJsGridController;
use App\Http\Requests\BackOffice\PeronnelManagement\PersonnelRequest;
use App\Http\Resources\BackOffice\PeronnelManagement\PersonnelCollection;
use App\Http\Resources\BackOffice\PeronnelManagement\PersonnelResource;
use App\Models\BackOffice\PeronnelManagement\Personnel;
use App\Models\BackOffice\PeronnelManagement\PersonnelExtra;
use App\Models\BackOffice\PeronnelManagement\PersonnelRole;
use App\Notifications\BackOffice\User\NewPersonnelAddedToRoleNotification;
use App\Notifications\BackOffice\User\PersonnelAccountDetailsNotification;
use App\Notifications\BackOffice\User\PersonnelDeletedNotification;
use App\Notifications\BackOffice\User\PersonnelRoleChangedNotification;
use App\Notifications\BackOffice\User\YourRoleChangedNotification;
use App\Notifications\General\GroupNotification\AuthorizedUsersNotificationGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PersonnelController extends SuperJsGridController
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, Personnel::class);

        $jsGrid_Controller = parent::getJsGridType(Personnel::class);
        $jsGrid_Controller->setApiBaseUrl(url(config('hhh_config.apiBaseUrls.backoffice.javascript')));
        $jsGrid_Controller->setApiSubUrl("personnel_management/personnel");
        $jsGrid_Controller->setItemProperties($jsGrid_Controller::grid_deleteConfirm, $jsGrid_Controller->markAsJavaCode(trans('confirm.Delete.jsGrid.default', ['col_name' => 'username'])));

        $fieldMaker = new jsGrid_FieldMaker(null, __("general.Row"));
        $fieldMaker->makeField_RowNumber();
        $jsGrid_Controller->putField($fieldMaker);

        $personnelRequest = new PersonnelRequest();
        $attributes = $personnelRequest->attributes();

        $fieldMaker = new jsGrid_FieldMaker(UsersTableEnum::Id->dbName(), __('thisApp.UserId'));
        $fieldMaker->makeField_Number();
        $fieldMaker->setPropertiesCollection($fieldMaker::ProCol_Unstorageable);
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "130");
        $fieldMaker->setItemProperties($fieldMaker::field_css, "ltr");
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(UsersTableEnum::Username->dbName(), __("auth.custom.Username"));
        $fieldMaker->makeField_Text();
        $attr = $attributes[UsersTableEnum::Username->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_minLength, config('hhh_config.validation.minLength.username'), trans('validation.min.string', ['attribute' => $attr, 'min' => config('hhh_config.validation.minLength.username')]));
        $fieldMaker->addValidate($fieldMaker::validator_maxLength, config('hhh_config.validation.maxLength.username'), trans('validation.max.string', ['attribute' => $attr, 'max' => config('hhh_config.validation.maxLength.username')]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(UsersTableEnum::Email->dbName(), __("auth.custom.Email"));
        $fieldMaker->makeField_Text();
        $attr = $attributes[UsersTableEnum::Email->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_minLength, config('hhh_config.validation.minLength.email'), trans('validation.min.string', ['attribute' => $attr, 'min' => config('hhh_config.validation.minLength.email')]));
        $fieldMaker->addValidate($fieldMaker::validator_maxLength, config('hhh_config.validation.maxLength.email'), trans('validation.max.string', ['attribute' => $attr, 'max' => config('hhh_config.validation.maxLength.email')]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'left');
        $jsGrid_Controller->putField($fieldMaker);


        $fieldMaker = new jsGrid_FieldMaker(UsersTableEnum::RoleId->dbName(), __("general.Role"));
        $options = DropdownListCreater::makeByModel(PersonnelRole::class, RolesTableEnum::Name->dbName())
            ->prepend("", -1)->useLable("name", "id")->sort(true)->get();
        $fieldMaker->makeField_Select('id', 'name', -1, 'number', $options);
        $attr = $attributes[UsersTableEnum::RoleId->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value > 0;}", trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->setItemProperties($fieldMaker::field_Align, 'center');
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PersonnelExtrasTableEnum::FirstName->dbName(), __("general.FirstName"));
        $fieldMaker->makeField_Text();
        $attr = $attributes[PersonnelExtrasTableEnum::FirstName->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_maxLength, 255, trans('validation.max.string', ['attribute' => $attr, 'max' => 255]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PersonnelExtrasTableEnum::LastName->dbName(), __("general.LastName"));
        $fieldMaker->makeField_Text();
        $attr = $attributes[PersonnelExtrasTableEnum::LastName->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_required, null, trans('validation.required', ['attribute' => $attr]));
        $fieldMaker->addValidate($fieldMaker::validator_maxLength, 255, trans('validation.max.string', ['attribute' => $attr, 'max' => 255]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PersonnelExtrasTableEnum::AliasName->dbName(), __("general.AliasName"));
        $fieldMaker->makeField_Text();
        $fieldMaker->addValidate($fieldMaker::validator_maxLength, 255, trans('validation.max.string', ['attribute' => $attr, 'max' => 255]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PersonnelExtrasTableEnum::Gender->dbName(), __("general.Gender"));
        $options = DropdownListCreater::makeByArray(GendersEnum::translatedArray())
            ->prepend("", "")->useLable("name", "key")->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $attr = $attributes[PersonnelExtrasTableEnum::Gender->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value != '';}", trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(UsersTableEnum::Status->dbName(), __("general.AccountStatus"));
        $options = DropdownListCreater::makeByArray(UsersStatusEnum::translatedArray())
            ->prepend("", "")->useLable("name", "key")->sort(true)->get();
        $fieldMaker->makeField_Select('key', 'name', "", 'string', $options);
        $attr = $attributes[UsersTableEnum::Status->dbName()];
        $fieldMaker->addValidate($fieldMaker::validator_function, "function(value) {return value != '';}", trans('validation.required', ['attribute' => $attr]));
        $jsGrid_Controller->putField($fieldMaker);

        $fieldMaker = new jsGrid_FieldMaker(PersonnelExtrasTableEnum::Descr->dbName(), __('general.Description'));
        $fieldMaker->makeField_Textarea();
        $fieldMaker->setItemProperties($fieldMaker::field_Width, "300");
        $jsGrid_Controller->putField($fieldMaker);


        $fieldMaker = new jsGrid_FieldMaker(null, null);
        $fieldMaker->makeField_Control();
        $jsGrid_Controller->putField($fieldMaker);


        $data = [
            config('hhh_config.keywords.jsGridJavaData')    =>  $jsGrid_Controller->create(),
        ];


        return view('hhh.BackOffice.pages.PersonnelManagement.Personnel.index', $data);
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
     *
     * @param  \App\Http\Requests\BackOffice\PeronnelManagement\PersonnelRoleRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function store(PersonnelRequest $request): JsonResponse
    {
        try {

            $personnel = new Personnel();
            $personnel->fill($request->all());
            $personnel[UsersTableEnum::Password->dbName()] = Hash::make($request->input(UsersTableEnum::Email->dbName()));
            $personnel[UsersTableEnum::Type->dbName()] = UsersTypesEnum::Personnel->name;

            if ($personnel->save()) {

                $personnelExtra = new PersonnelExtra();
                $personnelExtra->fill($request->all());
                $personnelExtra[PersonnelExtrasTableEnum::UserId->dbName()] = $personnel[UsersTableEnum::Id->dbName()];

                if ($personnelExtra->save()) {

                    $this->notifyStore($personnel);
                } else
                    $personnel->forceDelete();
            }
        } catch (\Throwable $th) {

            $personnel->forceDelete();
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        $request->merge(['id' => $personnel->id]);

        return JsonResponseHelper::successResponse(new PersonnelResource($request->all()), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Display the specified resource.
     */
    public function show(Personnel $personnel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Personnel $personnel)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BackOffice\PeronnelManagement\PersonnelRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function update(PersonnelRequest $request, Personnel $personnel): JsonResponse
    {
        try {

            $personnel = Personnel::find($request->input(UsersTableEnum::Id->dbName()));
            $personnel->fill($request->all());
            $this->notifyUpdate($personnel);
            $personnel->save();

            $personnelExtra = $personnel->personnelExtra;
            $personnelExtra->fill($request->all());
            $personnelExtra->save();
        } catch (\Throwable $th) {

            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }

        return JsonResponseHelper::successResponse(new PersonnelResource($request->all()), null, HttpResponseStatusCode::Created->value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\BackOffice\PeronnelManagement\PersonnelRequest $request
     * @return \Illuminate\Http\JsonResponse JsonResponse
     */
    public function destroy(PersonnelRequest $request): JsonResponse
    {
        if ($personnel = Personnel::find($request->input(UsersTableEnum::Id->dbName()))) {

            // Delete user settings
            $personnel->userSettings()->delete();

            $personnel->delete();

            /* Do not delete user extra, as the user has soft delete and maybe recovered
            $personnel->personnelExtra->delete(); */

            $this->notifyDestroy($personnel);

            return JsonResponseHelper::successResponse(null, trans('general.ItemSuccessfullyRemoved'));
        }

        return JsonResponseHelper::errorResponse(null, trans('general.NotFoundItem'), HttpResponseStatusCode::BadRequest->value);
    }

    /**
     * Return the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiIndex(Request $request)
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, Personnel::class);

        $pageSizeKey = config('hhh_config.keywords.pageSize');

        $pageSize = $request->input($pageSizeKey);

        return new PersonnelCollection(
            Personnel::ApiIndexCollection($request->input())
                ->paginate($pageSize)
        );
    }

    /**
     * Notify the necessary items in the update
     *
     * @param  \App\Models\BackOffice\PeronnelManagement\Personnel $personnel
     * @return void
     */
    private function notifyStore(personnel $personnel): void
    {
        // send account details via email to owner
        $personnel->notify(new PersonnelAccountDetailsNotification);

        // Authorized users group notification
        $affectedPersonnelId = $personnel[UsersTableEnum::Id->dbName()];

        $groupNotification =  new AuthorizedUsersNotificationGroup(
            new NewPersonnelAddedToRoleNotification(
                $affectedPersonnelId,
                $personnel[UsersTableEnum::RoleId->dbName()],
            ),
            AdminRoutesEnum::Personnel_Management->value,
            [PermissionAbilityEnum::update->name],
        );

        $groupNotification->send([$affectedPersonnelId, auth()->user()->id]);
    }

    /**
     * Notify the necessary items in the update
     *
     * @param  \App\Models\BackOffice\PeronnelManagement\Personnel $personnel
     * @return void
     */
    private function notifyUpdate(personnel $personnel): void
    {

        $roleId = UsersTableEnum::RoleId->dbName();
        if ($personnel->isDirty($roleId)) {

            $affectedPersonnelId = $personnel[UsersTableEnum::Id->dbName()];

            // Affectedd personnel notification
            $personnel->notify(new YourRoleChangedNotification(
                $personnel->getOriginal($roleId),
                $personnel[$roleId]
            ));

            // Authorized users group notification
            $groupNotification =  new AuthorizedUsersNotificationGroup(
                new PersonnelRoleChangedNotification(
                    $affectedPersonnelId,
                    $personnel->getOriginal($roleId),
                    $personnel[$roleId],
                ),
                AdminRoutesEnum::Personnel_Management->value,
                [PermissionAbilityEnum::update->name],
            );

            $groupNotification->send([$affectedPersonnelId, auth()->user()->id]);
        }
    }

    /**
     * Notify the necessary items in the update
     *
     * @param  \App\Models\BackOffice\PeronnelManagement\Personnel $personnel
     * @return void
     */
    private function notifyDestroy(personnel $personnel): void
    {
        // Authorized users group notification
        $affectedPersonnelId = $personnel[UsersTableEnum::Id->dbName()];

        $groupNotification =  new AuthorizedUsersNotificationGroup(
            new PersonnelDeletedNotification(
                $affectedPersonnelId,
                $personnel[UsersTableEnum::RoleId->dbName()],
            ),
            AdminRoutesEnum::Personnel_Management->value,
            [PermissionAbilityEnum::create->name, PermissionAbilityEnum::update->name],
        );

        $groupNotification->send([$affectedPersonnelId, auth()->user()->id]);
    }
}
