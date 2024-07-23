<?php

namespace App\Http\Controllers\Site;

use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Database\Tables\VerificationsTableEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Enums\Users\ClientProfileCheckEnum;
use App\Enums\Users\ContactMethodsEnum;
use App\Enums\Users\VerificationStatusEnum;
use App\Enums\Users\VerificationTypesEnum;
use App\HHH_Library\general\php\CarbonTimeDiffForHuman;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\general\php\Enums\CalendarTypeEnum;
use App\HHH_Library\general\php\Enums\GendersEnum as AppGendersEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\traits\Enums\EnumSession;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\GendersEnum as ExternalAdminGendersEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\ExternalAdminAPI;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\ApiResponseTest; // Do not clean this for fast action if need
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\Enums\TestResponseEnum; // Do not clean this for fast action if need
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\Models\Site\UserBetconstructProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\UserBetconstructProfileRequest;
use App\Mail\EmailVerificationMail;
use App\Models\BackOffice\Settings\Setting;
use App\Models\General\UserSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserBetconstructProfileController extends Controller
{
    use EnumSession;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(UserBetconstructProfile $userBetconstructProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Site\UserBetconstructProfile  $userBetconstructProfile
     * @return view
     */
    public function edit(UserBetconstructProfile $userBetconstructProfile)
    {
        $user = User::authUser();

        if (is_null($user))
            return redirect(SitePublicRoutesEnum::defaultLogin()->route());

        if ($user->isPersonnel())
            return redirect(AdminPublicRoutesEnum::Profile->route());

        $userProfile = $user->userBetconstruct;
        $userProfileExtra =  $userProfile->betconstructClient;

        $genderCol = ClientModelEnum::Gender->dbName();
        if ($userProfileExtra->$genderCol == ExternalAdminGendersEnum::Unknown->value)
            $userProfileExtra[$genderCol] = ExternalAdminGendersEnum::Male->value;

        $genderCollection = DropdownListCreater::makeByArray(ExternalAdminGendersEnum::translatedArray())
            ->notAllowedValues([ExternalAdminGendersEnum::Unknown->value])
            ->sort(true)->get();

        $provinceCollection = DropdownListCreater::makeByArray(__('IranCities.Provinces'))
            ->sort(true)->useReverseList()->prepend(__('PagesContent_UserBetconstructProfile.form.province_internal.placeholder'), null)->get();

        $cities = __('IranCities.Cities.' . $userProfileExtra[ClientModelEnum::ProvinceInternal->dbName()]);
        if (is_array($cities)) {
            $citiesCollection = DropdownListCreater::makeByArray($cities)
                ->sort(true)->useReverseList()->prepend(__('PagesContent_UserBetconstructProfile.form.city_internal.placeholder'), null)->get();
        } else
            $citiesCollection = [];

        $jobFieldsCollection = DropdownListCreater::makeByArray(__('thisApp.JobFields'))
            ->sort(true)->useReverseList()->prepend(__('PagesContent_UserBetconstructProfile.form.job_field_internal.placeholder'), null)->get();

        $contactMethodsCollection = DropdownListCreater::makeByArray(ContactMethodsEnum::translatedArray())
            ->useReverseList()->get();

        $callerGenderCollection = DropdownListCreater::makeByArray(AppGendersEnum::translatedArray())
            ->useReverseList()->get();

        $emailVerificationStatus = $this->getEmailVerificationStatus($user, $userProfileExtra);
        if ($emailVerificationStatus == VerificationStatusEnum::UnderVerify->name) {

            $emailCol = ClientModelEnum::Email->dbName();
            $verification = VerificationTypesEnum::Email->getVerification($user, $userProfileExtra->$emailCol);
            $userProfileExtra[$emailCol] = $verification[VerificationsTableEnum::NewValue->dbName()];

            $nextVerificationEmailTime = (new CarbonTimeDiffForHuman(Carbon::now(), $verification[VerificationsTableEnum::ValidUntil->dbName()]))
                ->ignoreSuffixes()
                ->getDiff();

            $nextVerificationEmailMsg = __('PagesContent_UserBetconstructProfile.messages.verificationEmailNotReceive', ['remainingTime' => $nextVerificationEmailTime]);
        } else
            $nextVerificationEmailMsg = null;

        $data = [
            'SitePublicRoutesEnum' => SitePublicRoutesEnum::class,
            'UsersTableEnum' => UsersTableEnum::class,
            'ClientExtrasTableEnum' => ClientModelEnum::class,
            'userProfile' => $userProfile,
            'userProfileExtra' => $userProfileExtra,
            'genderCollection' => $genderCollection,
            'provinceCollection' => $provinceCollection,
            'citiesCollection' => $citiesCollection,
            'jobFieldsCollection' => $jobFieldsCollection,
            'contactMethodsCollection' => $contactMethodsCollection,
            'callerGenderCollection' => $callerGenderCollection,
            'emailVerificationStatus' => $emailVerificationStatus,
            'nextVerificationEmailMsg' => $nextVerificationEmailMsg,
            'isFurtherInformationTabCompleted' => ClientProfileCheckEnum::FurtherInformationTab->isCompleted($user),
            'isEmailTabCompleted' => ClientProfileCheckEnum::EmailTab->isCompleted($user),
        ];

        $data = array_merge($data, $this->getUserSettings());

        return view('hhh.Site.pages.UserBetconstructProfile.edit.index', $data);
    }


    /**
     * Get user settings data for settings tab
     *
     * @return array
     */
    private function getUserSettings(): array
    {
        // Settings & Default values
        $case = AppSettingsEnum::CommunityTimeZone;
        $setting[$case->name] = UserSetting::get($case, "");
        $defaults[$case->name] = Setting::get($case);

        $case = AppSettingsEnum::CommunityCalendarType;
        $setting[$case->name] = UserSetting::get($case, "");
        $defaults[$case->name] = constant(CalendarTypeEnum::class . '::' . Setting::get($case))->translate();

        $setting = json_decode(json_encode($setting), false);
        $defaults = json_decode(json_encode($defaults), false);

        // Permissions for change items
        $canClientChangeTimeZone = Setting::get(AppSettingsEnum::canClientChangeTimeZone);
        $canClientChangeCalendarType = Setting::get(AppSettingsEnum::canClientChangeCalendarType);

        $calendarTypeDropdown = DropdownListCreater::makeByArray(CalendarTypeEnum::translatedArray())
            ->prepend(__('general.SystemDefault'), "")->sort(true)->get();

        return [

            'AppSettingsEnum' => AppSettingsEnum::class,
            'settingsTabDispaly' => $canClientChangeTimeZone || $canClientChangeCalendarType,
            'canClientChangeTimeZone' => $canClientChangeTimeZone,
            'canClientChangeCalendarType' => $canClientChangeCalendarType,
            'calendarTypeDropdown' => $calendarTypeDropdown,
            'setting' => $setting,
            'defaults' => $defaults,
        ];
    }

    /**
     * Get email verification status
     *
     * @param  \App\Models\User $user
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $userProfileExtra
     * @return string
     */
    private function getEmailVerificationStatus(User $user, BetconstructClient $betconstructClient): string
    {
        $clientEmail = $betconstructClient[ClientModelEnum::Email->dbName()];
        $verification = VerificationTypesEnum::Email->getVerification($user, $clientEmail);

        if (!is_null($user[UsersTableEnum::EmailVerifiedAt->dbName()])) {

            return is_null($verification) ? VerificationStatusEnum::Verified->name : VerificationStatusEnum::UnderVerify->name;
        } else {

            if (empty($clientEmail)) {
                // Client does not have email (Not required field)
                return is_null($verification) ? VerificationStatusEnum::NoNeedVerify->name : VerificationStatusEnum::UnderVerify->name;
            } else {
                // Client has unverified email

                return is_null($verification) ? VerificationStatusEnum::NeedVerify->name : VerificationStatusEnum::UnderVerify->name;
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Site\UserBetconstructProfileRequest  $request
     * @param  \App\Models\Site\UserBetconstructProfile  $userBetconstructProfile
     * @return view
     */
    public function update(UserBetconstructProfileRequest $request, UserBetconstructProfile $userBetconstructProfile)
    {
        try {

            $user = User::authUser();

            if (is_null($user))
                return redirect(SitePublicRoutesEnum::defaultLogin()->route());

            if (!$user->isClient())
                Auth::logout();

            /** @var BetconstructClient $betconstructClient*/
            $betconstructClient = $user->userExtra;

            if (is_null($betconstructClient))
                Auth::logout();

            $tabpanel = $request->input('_tabpanel');

            if ($tabpanel == "Account") {
                // There is nothing to do
            } else if ($tabpanel == "FurtherInformation") {

                return  $this->updateFurtherInformation($request, $betconstructClient);
            } else if ($tabpanel == "ChangeEmail") {

                return $this->updateChangeEmail($request, $betconstructClient);
            } else if ($tabpanel == "Password") {

                return $this->updatePassword($request, $betconstructClient);
            } else if ($tabpanel == "Photo") {

                return $this->updateProfilePhoto($request, $user->id);
            } else if ($tabpanel == "Settings") {

                return $this->updateSettings($request);
            }
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->withErrors([$th->getMessage()]);
        }

        return redirect()->back()
            ->with('success', trans('PagesContent_UserBetconstructProfile.messages.SavedSuccessfully'));
    }

    /**
     * Update further information tab
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $betconstructClient
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function updateFurtherInformation(Request $request, BetconstructClient $betconstructClient): Redirector|RedirectResponse
    {
        // Database columns
        $genderCol = ClientModelEnum::Gender->dbName();
        $birthDateStampCol = ClientModelEnum::BirthDateStamp->dbName();
        $ibanCol = ClientModelEnum::IBAN->dbName();
        $provinceInternalCol = ClientModelEnum::ProvinceInternal->dbName();
        $cityInternalCol = ClientModelEnum::CityInternal->dbName();
        $jobFieldInternalCol = ClientModelEnum::JobFieldInternal->dbName();
        $contactNumbersInternalCol = ClientModelEnum::ContactNumbersInternal->dbName();
        $contactMethodsInternalCol = ClientModelEnum::ContactMethodsInternal->dbName();
        $callerGenderInternalCol = ClientModelEnum::CallerGenderInternal->dbName();

        $betconstructClient[$genderCol] = $request->input($genderCol);
        $betconstructClient[$birthDateStampCol] = $request->input($birthDateStampCol);
        $betconstructClient[$ibanCol] = $request->input($ibanCol);

        $betconstructClient[$provinceInternalCol] = $request->input($provinceInternalCol);
        $betconstructClient[$cityInternalCol] = $request->input($cityInternalCol);
        $betconstructClient[$jobFieldInternalCol] = $request->input($jobFieldInternalCol);
        $betconstructClient[$contactNumbersInternalCol] = $request->input($contactNumbersInternalCol);
        $betconstructClient[$contactMethodsInternalCol] = $request->input($contactMethodsInternalCol);
        $betconstructClient[$callerGenderInternalCol] = $request->input($callerGenderInternalCol);

        $updateResult = ClientModelEnum::updateBetconstructClientData($betconstructClient);

        if (is_string($updateResult)) {
            // Betconstruct error
            return redirect()->back()->withInput()->withErrors([$updateResult]);
        }

        return redirect()->back()
            ->with('success', trans('PagesContent_UserBetconstructProfile.messages.SavedSuccessfully'));
    }

    /**
     * Update change email tab
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $betconstructClient
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function updateChangeEmail(Request $request, BetconstructClient $betconstructClient): Redirector|RedirectResponse
    {
        $user = User::authUser();

        if (is_null($user))
            return redirect(SitePublicRoutesEnum::defaultLogin()->route());

        // Database columns
        $emailCol = ClientModelEnum::Email->dbName();
        $emailVerificationCodeKey = "emailVerificationCode";

        $betconstructClient =  $user->betconstructClient;

        $oldEmail = $betconstructClient->$emailCol;

        $verification = VerificationTypesEnum::Email->getVerification($user, $oldEmail);
        $newEmail = is_null($verification) ? $request->input($emailCol) : $verification[VerificationsTableEnum::NewValue->dbName()];
        $emailVerificationCode = $request->input($emailVerificationCodeKey);

        if ($newEmail == $oldEmail && ClientProfileCheckEnum::LastEmailVerification->isCompleted($user))
            return redirect()->back()->withErrors([]);

        $errorWrongInputData = redirect()->back()->withInput()->withErrors([__('thisApp.Errors.WrongInputData')]);

        if (empty($newEmail))
            return $errorWrongInputData;

        # Start to verify Email
        $emailDuplicateError = ClientModelEnum::checkEmailDuplicate($betconstructClient, $newEmail);
        if (!is_null($emailDuplicateError))
            return redirect()->back()->withInput()->withErrors([$emailDuplicateError]);

        $verification = VerificationTypesEnum::Email->getVerification($user, $oldEmail, $newEmail);

        if (is_null($verification)) {
            // User does not have verification record

            $this->sendEmailVerification($user, $oldEmail, $newEmail);

            return redirect()->back()->withInput()->withErrors([])
                ->with('success', trans('PagesContent_UserBetconstructProfile.messages.verificationEmailSent'));
        }

        if (empty($emailVerificationCode))
            return $errorWrongInputData;

        if ($oldEmail != $verification[VerificationsTableEnum::OldValue->dbName()])
            return $errorWrongInputData;

        if ($newEmail != $verification[VerificationsTableEnum::NewValue->dbName()])
            return $errorWrongInputData;

        if ($emailVerificationCode != $verification[VerificationsTableEnum::Code->dbName()])
            return $errorWrongInputData;

        $betconstructClient[$emailCol] = $newEmail;

        $updateResult = ClientModelEnum::updateBetconstructClientData($betconstructClient);

        if (is_string($updateResult)) {
            // Betconstruct error
            return redirect()->back()->withInput()->withErrors([$updateResult]);
        }

        $user[UsersTableEnum::EmailVerifiedAt->dbName()] = Carbon::now();
        $user->save();

        $verification->delete();

        return redirect()->back()->withErrors([])
            ->with('success', trans('PagesContent_UserBetconstructProfile.messages.SavedSuccessfully'));
    }

    /**
     * Update password tab
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $betconstructClient
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function updatePassword(Request $request, BetconstructClient $betconstructClient): Redirector|RedirectResponse
    {
        $newPassword = $request->input('new_password');

        if (!empty($newPassword)) {

            $betconstructClient[ClientModelEnum::Password->dbName()] = $newPassword;

            $updateClientResponse = ExternalAdminAPI::changeClientPassword($betconstructClient[ClientModelEnum::Id->dbName()], $betconstructClient[ClientModelEnum::Login->dbName()], $newPassword);

            if ($updateClientResponse->getStatus()->name === ApiStatusEnum::Success->name) {

                $user = $betconstructClient->user;

                $user[UsersTableEnum::Password->dbName()] = Hash::make($newPassword);
                $user->save();

                $betconstructClient = ClientModelEnum::fillModel($updateClientResponse->getData(), $betconstructClient);
                $betconstructClient[ClientModelEnum::Password->dbName()] = Crypt::encrypt($newPassword);
                $betconstructClient->save();

                EnumSession::logoutUserFromAllDevices(User::authUser());

                return redirect()->back()->withErrors([])
                    ->with('success', trans('PagesContent_UserBetconstructProfile.messages.SavedSuccessfully'));
            } else {
                $errorMessage = $updateClientResponse->getErrorMessage();
                return redirect()->back()->withInput()->withErrors([$errorMessage]);
            }
        }


        return redirect()->back()->withErrors([]);
    }

    /**
     * Update profile photo tab
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $userId
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function updateProfilePhoto(Request $request, int $userId): Redirector|RedirectResponse
    {
        /** @var UserBetconstructProfile $userBetconstructProfile */
        $userBetconstructProfile = UserBetconstructProfile::find($userId);

        /************** File ******************/
        $photoField = UsersTableEnum::ProfilePhotoName->dbName();
        $lastFile = $userBetconstructProfile->getPhotoFileAssistant(false);
        $storedFileName = $lastFile->storeUploadedFile($request, $photoField);

        if ($storedFileName != null)
            $userBetconstructProfile[$photoField] = $storedFileName;
        /************** File END ******************/

        $userBetconstructProfile->save();

        return redirect()->back()
            ->with('success', trans('PagesContent_UserBetconstructProfile.messages.SavedSuccessfully'));
    }

    /**
     * Update user settings tab
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function updateSettings(Request $request): Redirector|RedirectResponse
    {
        $case = AppSettingsEnum::CommunityTimeZone;
        UserSetting::saveItem($case, $request->input($case->name));

        $case = AppSettingsEnum::CommunityCalendarType;
        UserSetting::saveItem($case, $request->input($case->name));

        return redirect()->back()
            ->with('success', trans('PagesContent_UserBetconstructProfile.messages.SavedSuccessfully'));
    }

    /**
     * Send email verification
     *
     * @param  \App\Models\User $user
     * @param  ?string $oldEmail
     * @param  ?string $newEmail
     * @return bool
     */
    private function sendEmailVerification(?User $user, ?string $oldEmail, ?string $newEmail): bool
    {
        if (is_null($user))
            return false;

        $newEmail = trim($newEmail);

        if (empty($newEmail))
            return false;

        if ($newEmail == $oldEmail && ClientProfileCheckEnum::LastEmailVerification->isCompleted($user))
            return false;

        $verification = VerificationTypesEnum::Email->makeVerificationRecord(10, $user, $oldEmail, $newEmail);

        try {
            Mail::to($newEmail)->send(new EmailVerificationMail($verification->id));
            return true;
        } catch (\Throwable $th) {

            $error = sprintf(
                "Error:\n%s\nVerification:\n%s",
                $th->getMessage(),
                json_encode($verification),
            );

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $error,
                'Sending email verification issue'
            );
        }

        return false;
    }

    /********************* API Requests *********************/

    /**
     * Attach default data and make final response data
     *
     * @param  ?array $data
     * @return array
     */
    private function makeApiResponseData(?array $data): array
    {
        $defaultData = [
            'csrfToken' => csrf_token(),
            'debugMode' => (bool) config('app.debug'),
        ];

        return (is_null($data)) ? $defaultData : array_merge($defaultData, $data);
    }

    /**
     * Get cities for API
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiGetCities(Request $request): JsonResponse
    {
        $province = $request->input('province');

        $citiesCollection = [];

        if (!empty($province)) {

            $cities = __('IranCities.Cities.' . $province);
            if (is_array($cities))
                $citiesCollection = DropdownListCreater::makeByArray($cities)
                    ->sort(true)->useReverseList()->prepend(__('PagesContent_UserBetconstructProfile.form.city_internal.placeholder'), "")->get();
        }

        $data = [
            'cities' => $citiesCollection,
        ];
        return JsonResponseHelper::successResponse($this->makeApiResponseData($data), 'Success');
    }

    /**
     * Send email verification for API
     *
     * @param  \App\Http\Requests\Site\UserBetconstructProfileRequest $request
     * @return \lluminate\Http\JsonResponse
     */
    public function apiSendEmailVerification(UserBetconstructProfileRequest $request): JsonResponse
    {
        $newEmail = trim($request->input(ClientModelEnum::Email->dbName()));

        $user = User::authUser();

        $userExtra = $user->userExtra;
        $oldEmail = $userExtra[ClientModelEnum::Email->dbName()];

        if ($newEmail == $oldEmail && ClientProfileCheckEnum::LastEmailVerification->isCompleted($user))
            return JsonResponseHelper::errorResponse('thisApp.Errors.Profile.EmailVerified', __('thisApp.Errors.Profile.EmailVerified'), HttpResponseStatusCode::UnprocessableEntity->value, $this->makeApiResponseData(['refreshPage' => true]));

        if ($this->sendEmailVerification($user, $oldEmail, $newEmail))
            return JsonResponseHelper::successResponse($this->makeApiResponseData(null), __('PagesContent_UserBetconstructProfile.messages.verificationEmailSent'));
        else
            return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'), HttpResponseStatusCode::InternalServerError->value, $this->makeApiResponseData(['refreshPage' => true]));
    }
    /********************* API Requests END *********************/
}
