<?php

namespace App\Http\Controllers\Site\Auth\Betconstruct;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ReferralsTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Database\Tables\VerificationsTableEnum;
use App\Enums\General\CurrencyEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Session\GeneralSessionsEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Enums\Users\ClientRegistrationAvailabelFieldsEnum;
use App\Enums\Users\ContactMethodsEnum;
use App\Enums\Users\VerificationTypesEnum;
use App\HHH_Library\general\php\ArrayHelper;
use App\HHH_Library\general\php\CarbonTimeDiffForHuman;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\general\php\Enums\CalendarTypeEnum;
use App\HHH_Library\general\php\Enums\CountryEnum;
use App\HHH_Library\general\php\Enums\GendersEnum as AppGendersEnum;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\GendersEnum as ExternalAdminGendersEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\ExternalAdminAPI;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\ApiResponseTest as ExternalAdminApiResponseTest; // Do not remove this line, this is a test for quick actions
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ClientSwarmModelEnum;
use App\HHH_Library\ThisApp\API\JustCall\Helpers\Tests\ApiResponseTest as JustCallApiResponseTest; // Do not remove this line, this is a test for quick actions
use App\HHH_Library\ThisApp\API\JustCall\Helpers\Tests\Enums\TestResponseEnum; // Do not remove this line, this is a test for quick actions
use App\HHH_Library\ThisApp\API\JustCall\JustCallAPI;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Http\Requests\Site\Auth\RegisterAttemptRequest;
use App\Mail\EmailVerificationMail;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use App\Models\BackOffice\Referral\Referral;
use App\Models\General\Verification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Mail;

class RegisterBetconstructController extends SuperController
{
    private const CURRENT_STEP = 1, NEXT_STEP = 2, PREVIOUS_STEP = 3;

    private const
        IS_MOBILE_VERIFIED = "isMobileVerified",
        IS_EMAIL_VERIFIED = "isEmailVerified";

    const
        STEP_GET_MOBILE_NUMBER      = "GetMobileNumber",
        STEP_VERIFY_MOBILE_NUMBER   = "VerifyMobileNumber",
        STEP_GET_EMAIL              = "GetEmail",
        STEP_VERIFY_EMAIL           = "VerifyEmail",
        STEP_ACCOUNT_DATA           = "AccountData",
        STEP_FURTHER_INFORMATION    = "FurtherInformation",
        STEP_SUBMIT                 = "SubmitRegistration";


    /**
     * Display login form.
     */
    public function index()
    {

        if (!AppSettingsEnum::CommunityRegistrationIsActive->getValue(false))
            return redirect(SitePublicRoutesEnum::MainPage->url());

        $currentStep = $this->getStep(self::CURRENT_STEP);

        if ($currentStep == self::STEP_SUBMIT)
            return $this->submitRegistration();

        $stepExtraData = $this->getStepExtraData($currentStep);

        if (is_null($stepExtraData))
            return $this->index(); // Need to rebuild the index page

        $registrationFields = AppSettingsEnum::CommunityRegistrationFields->getValue();

        $data = [
            'ClientExtrasTableEnum'     => ClientModelEnum::class,
            'registrationFields'        => is_null($registrationFields) ? [] : json_decode($registrationFields),
            'isFristStep'               => $this->isFristStep(),
            'isLastStep'                => $this->isLastStep(),
            'stepsProgressList'         => $this->getStepsProgressList(),
            'tabPanel'                  => $currentStep,
        ];

        $data = array_merge($data, $stepExtraData);

        return view('hhh.Site.pages.auth.Betconstruct.Registration.index', $data);
    }

    /**
     * Go back step request by client
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function goBackStepRequest()
    {
        $this->moveStepBackward();
        $currentStep = $this->getStep(self::CURRENT_STEP);

        if ($currentStep == self::STEP_VERIFY_MOBILE_NUMBER)
            $this->moveStepBackward();

        if ($currentStep == self::STEP_VERIFY_EMAIL)
            $this->moveStepBackward();

        $inputs = GeneralSessionsEnum::SiteRegistrationFormData->getSession([]);
        return redirect(SitePublicRoutesEnum::RegisterBetconstruct->url())->withInput($inputs);
    }

    /**
     * Get extra data for step
     *
     * @param  mixed $step
     * @return ?array  array: data, null: if you need to rebuild the index page
     */
    private function getStepExtraData(?string $step): ?array
    {
        // STEP_GET_MOBILE_NUMBER
        if ($step == self::STEP_GET_MOBILE_NUMBER) {

            $sessionMobileNumber = GeneralSessionsEnum::SiteRegistrationMobileNumber->getSession();

            return [
                'underVerifyMobileNumber' => $sessionMobileNumber,
            ];
        }
        // STEP_VERIFY_MOBILE_NUMBER
        else if ($step == self::STEP_VERIFY_MOBILE_NUMBER) {

            $sessionMobileNumber = GeneralSessionsEnum::SiteRegistrationMobileNumber->getSession();
            $nextMobileVerificationTime = is_null($sessionMobileNumber) ? null : $this->getNextVerificationTime(VerificationTypesEnum::Mobile, $sessionMobileNumber);

            if (is_null($nextMobileVerificationTime)) {

                $this->moveStepBackward();
                return null;
            }

            return [
                'underVerifyMobileNumber'   => $sessionMobileNumber,
                'nextMobileVerificationMsg' => $nextMobileVerificationTime,
            ];
        }
        // STEP_GET_EMAIL
        if ($step == self::STEP_GET_EMAIL) {

            $sessionEmail = GeneralSessionsEnum::SiteRegistrationEmail->getSession();

            return [
                'underVerifyEmail' => $sessionEmail,
            ];
        }
        // STEP_VERIFY_EMAIL
        else if ($step == self::STEP_VERIFY_EMAIL) {

            $sessionEmail = GeneralSessionsEnum::SiteRegistrationEmail->getSession();
            $nextEmailVerificationTime = is_null($sessionEmail) ? null : $this->getNextVerificationTime(VerificationTypesEnum::Email, $sessionEmail);

            if (is_null($nextEmailVerificationTime)) {

                $this->moveStepBackward();
                return null;
            }

            return [
                'underVerifyEmail'          => $sessionEmail,
                'nextEmailVerificationMsg'  => $nextEmailVerificationTime,
            ];
        }
        // STEP_ACCOUNT_DATA
        else if ($step == self::STEP_ACCOUNT_DATA) {

            $availabelCurrencies = json_decode(AppSettingsEnum::CommunityRegistrationAvailableCurrencies->getValue());
            $allCurrencies = CurrencyEnum::getCollectionList(true);
            $currenciesCollection = [];

            foreach ($allCurrencies as $key => $text) {

                if (in_array($key, $availabelCurrencies)) {
                    $currenciesCollection[$key] = $text;
                }
            }

            $currenciesCollection = DropdownListCreater::makeByArray($currenciesCollection)
                ->useReverseList()
                ->sort(true, false)->get();

            return [
                'currenciesCollection' => $currenciesCollection,
                'defaultCurrency' => AppSettingsEnum::CommunityRegistrationDefaultCurrency->getValue(),
            ];
        }
        // STEP_FURTHER_INFORMATION
        else if ($step == self::STEP_FURTHER_INFORMATION) {

            $genderCollection = DropdownListCreater::makeByArray(ExternalAdminGendersEnum::translatedArray())
                ->notAllowedValues([ExternalAdminGendersEnum::Unknown->value])
                ->sort(false)->get();

            $defalutCalendar = CalendarTypeEnum::getCase(AppSettingsEnum::CommunityCalendarType->getValue());

            $provinceCollection = DropdownListCreater::makeByArray(__('IranCities.Provinces'))
                ->sort(true)->useReverseList()->prepend(__('PagesContent_UserBetconstructProfile.form.province_internal.placeholder'), null)->get();

            $contactMethodsCollection = DropdownListCreater::makeByArray(ContactMethodsEnum::translatedArray())
                ->useReverseList()->get();

            $callerGenderCollection = DropdownListCreater::makeByArray(AppGendersEnum::translatedArray())
                ->useReverseList()->get();

            return [
                'genderCollection' => $genderCollection,
                'defalutCalendarName' => $defalutCalendar->translate(),
                'provinceCollection' => $provinceCollection,
                'citiesCollection' => [],
                'contactNumbersCollection' => [GeneralSessionsEnum::SiteRegistrationMobileNumber->getSession()],
                'contactMethodsCollection' => $contactMethodsCollection,
                'callerGenderCollection' => $callerGenderCollection,
            ];
        }

        return [];
    }

    /**
     * Handle login request attempt.
     *
     * @param  \App\Http\Requests\Site\Auth\RegisterAttemptRequest $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function attempt(RegisterAttemptRequest $request)
    {

        if (!AppSettingsEnum::CommunityRegistrationIsActive->getValue(false))
            return redirect(SitePublicRoutesEnum::MainPage->url());

        $tabPanel = $request->input('_tabpanel');

        try {

            switch ($tabPanel) {
                case self::STEP_GET_MOBILE_NUMBER:
                    return $this->getMobileNumberAttempt($request);
                    break;
                case self::STEP_VERIFY_MOBILE_NUMBER:
                    return $this->verifyMobileNumberAttempt($request);
                    break;
                case self::STEP_GET_EMAIL:
                    return $this->getEmailAttempt($request);
                    break;
                case self::STEP_VERIFY_EMAIL:
                    return $this->verifyEmailAttempt($request);
                    break;
                case self::STEP_ACCOUNT_DATA:
                    return $this->accountDataAttempt($request);
                    break;
                case self::STEP_FURTHER_INFORMATION:
                    return $this->furtherInformationAttempt($request);
                    break;
            }
        } catch (\Throwable $th) {
            return $this->redirectBackWithErrors($th->getMessage());
        }

        return $this->redirectBackWithErrors(trans('general.error.BadRequest'));
    }

    /**
     * Get mobile number step
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function getMobileNumberAttempt(Request $request)
    {
        $mobilePhone = $request->input(ClientModelEnum::MobilePhone->dbName());

        // Check phone number duplicate. The Betconstruct stored mobile phones in phones data!!
        $duplicateError = ClientModelEnum::checkPhoneNumberDuplicate($mobilePhone, __('bc_api.DuplicateMobileNumber'));
        if (!is_null($duplicateError))
            return $this->redirectBackWithErrors($duplicateError);

        // Check mobile phone number duplicate
        $duplicateError = ClientModelEnum::checkMobileNumberDuplicate($mobilePhone);
        if (!is_null($duplicateError))
            return $this->redirectBackWithErrors($duplicateError);

        GeneralSessionsEnum::SiteRegistrationMobileNumber->setSession($mobilePhone);

        if (AppSettingsEnum::CommunityRegistrationMobileVerificationIsRequired->getValue()) {

            # Create Verification code
            $sendingSmsResult = $this->sendVerificationSMS($mobilePhone);

            if (!is_null($sendingSmsResult))
                return $this->redirectBackWithErrors($sendingSmsResult);

            $this->moveStepForward();

            return redirect()->back()->withInput()->withSuccess(__('PagesContent_RegisaterationBetconstruct.messages.verificationSmsSent'));
        }

        $this->updateSessionFormData([
            ClientModelEnum::Phone->dbName()        => $mobilePhone,
            ClientModelEnum::MobilePhone->dbName()  => $mobilePhone,
            self::IS_MOBILE_VERIFIED                => false,
        ]);

        $this->moveStepForward();
        return redirect()->back();
    }

    /**
     * Verify mobile number attempt step
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function verifyMobileNumberAttempt(Request $request)
    {
        $mobileVerificationCode = $request->input("MobileVerificationCode");

        $mobileNumber = GeneralSessionsEnum::SiteRegistrationMobileNumber->getSession();

        if (!empty($mobileNumber)) {

            if ($verification = VerificationTypesEnum::Mobile->getVerification(null, null, $mobileNumber)) {

                if ($verification[VerificationsTableEnum::Code->dbName()] == $mobileVerificationCode) {

                    $verification->delete();
                    $this->moveStepForward();

                    $this->updateSessionFormData([
                        ClientModelEnum::Phone->dbName()        => $mobileNumber,
                        ClientModelEnum::MobilePhone->dbName()  => $mobileNumber,
                        self::IS_MOBILE_VERIFIED                => true,
                    ]);

                    return redirect()->back();
                }
            }

            if (is_null($this->getNextVerificationTime(VerificationTypesEnum::Mobile, $mobileNumber)))
                $this->moveStepBackward();
        }

        return $this->redirectBackWithErrors(['MobileVerificationCode' => __('PagesContent_RegisaterationBetconstruct.messages.verificationFailed')]);
    }

    /**
     * Get email step
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function getEmailAttempt(Request $request)
    {
        $email = $request->input(ClientModelEnum::Email->dbName());

        // Check email duplicate
        $duplicateError = ClientModelEnum::checkEmailDuplicate(null, $email);
        if (!is_null($duplicateError))
            return $this->redirectBackWithErrors($duplicateError);

        GeneralSessionsEnum::SiteRegistrationEmail->setSession($email);

        if (AppSettingsEnum::CommunityRegistrationEmailVerificationIsRequired->getValue()) {

            # Create Verification code
            $sendingVerificationEmailResult = $this->sendVerificationEmail($email);

            if (!is_null($sendingVerificationEmailResult))
                return $this->redirectBackWithErrors($sendingVerificationEmailResult);

            $this->moveStepForward();

            return redirect()->back()->withInput()->withSuccess(__('PagesContent_RegisaterationBetconstruct.messages.verificationEmailSent'));
        }

        $this->updateSessionFormData([
            ClientModelEnum::Email->dbName()    => $email,
            self::IS_EMAIL_VERIFIED             => false,
        ]);

        $this->moveStepForward();
        return redirect()->back();
    }

    /**
     * Verify email step
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function verifyEmailAttempt(Request $request)
    {
        $emailVerificationCode = $request->input("EmailVerificationCode");

        $email = GeneralSessionsEnum::SiteRegistrationEmail->getSession();

        if (!empty($email)) {

            if ($verification = VerificationTypesEnum::Email->getVerification(null, null, $email)) {

                if ($verification[VerificationsTableEnum::Code->dbName()] == $emailVerificationCode) {

                    $verification->delete();
                    $this->moveStepForward();

                    $this->updateSessionFormData([
                        ClientModelEnum::Email->dbName()    => $email,
                        self::IS_EMAIL_VERIFIED             => true,
                    ]);

                    return redirect()->back();
                }
            }

            if (is_null($this->getNextVerificationTime(VerificationTypesEnum::Email, $email)))
                $this->moveStepBackward();
        }
        return $this->redirectBackWithErrors(['EmailVerificationCode' => __('PagesContent_RegisaterationBetconstruct.messages.verificationFailed')]);
    }

    /**
     * Account data step
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function accountDataAttempt(Request $request)
    {
        // Check username duplicate
        $duplicateError = ClientModelEnum::checkUsernameDuplicate($request->input(ClientModelEnum::Login->dbName()));
        if (!is_null($duplicateError))
            return $this->redirectBackWithErrors($duplicateError);

        $this->updateSessionFormData([

            ClientModelEnum::Login->dbName()        => $request->input(ClientModelEnum::Login->dbName()),
            ClientModelEnum::Password->dbName()     => $request->input("regPassword"),
            'regPassword'                           => $request->input("regPassword"), // Used when client request to go back step
            ClientModelEnum::FirstName->dbName()    => $request->input(ClientModelEnum::FirstName->dbName()),
            ClientModelEnum::LastName->dbName()     => $request->input(ClientModelEnum::LastName->dbName()),
            ClientModelEnum::CurrencyId->dbName()   => $request->input(ClientModelEnum::CurrencyId->dbName()),
        ]);

        $this->moveStepForward();
        return redirect()->back();
    }

    /**
     * Further information attempt step
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function furtherInformationAttempt(Request $request)
    {
        $this->updateSessionFormData([

            ClientModelEnum::Gender->dbName()                   => $request->input(ClientModelEnum::Gender->dbName()),
            ClientModelEnum::BirthDateStamp->dbName()           => $request->input(ClientModelEnum::BirthDateStamp->dbName()),
            ClientModelEnum::ProvinceInternal->dbName()         => $request->input(ClientModelEnum::ProvinceInternal->dbName()),
            ClientModelEnum::CityInternal->dbName()             => $request->input(ClientModelEnum::CityInternal->dbName()),
            ClientModelEnum::ContactNumbersInternal->dbName()   => $request->input(ClientModelEnum::ContactNumbersInternal->dbName()),
            ClientModelEnum::ContactMethodsInternal->dbName()   => $request->input(ClientModelEnum::ContactMethodsInternal->dbName()),
            ClientModelEnum::CallerGenderInternal->dbName()     => $request->input(ClientModelEnum::CallerGenderInternal->dbName()),

        ]);

        $this->moveStepForward();
        return redirect()->back();
    }

    /**
     * Submit registration
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function submitRegistration()
    {

        if (!$this->validateFormData())
            return $this->resetRegistration();

        // Database columns
        $phoneCol = ClientModelEnum::Phone->dbName();
        $mobilePhoneCol = ClientModelEnum::MobilePhone->dbName();
        $emailCol = ClientModelEnum::Email->dbName();
        $loginCol = ClientModelEnum::Login->dbName();
        $passwordCol = ClientModelEnum::Password->dbName();
        $firstNameCol = ClientModelEnum::FirstName->dbName();
        $lastNameCol = ClientModelEnum::LastName->dbName();
        $currencyIdCol = ClientModelEnum::CurrencyId->dbName();
        $genderCol = ClientModelEnum::Gender->dbName();
        $birthDateStampCol = ClientModelEnum::BirthDateStamp->dbName();
        $provinceInternalCol = ClientModelEnum::ProvinceInternal->dbName();
        $cityInternalCol = ClientModelEnum::CityInternal->dbName();
        $contactNumbersInternalCol = ClientModelEnum::ContactNumbersInternal->dbName();
        $contactMethodsInternalCol = ClientModelEnum::ContactMethodsInternal->dbName();
        $callerGenderInternalCol = ClientModelEnum::CallerGenderInternal->dbName();
        $regionCodeCol = ClientModelEnum::RegionCode->dbName();

        $fillables = [
            $phoneCol,
            $mobilePhoneCol,
            $emailCol,
            $loginCol,
            $passwordCol,
            $firstNameCol,
            $lastNameCol,
            $currencyIdCol,
            $genderCol,
            $birthDateStampCol,
            $provinceInternalCol,
            $cityInternalCol,
            $contactNumbersInternalCol,
            $contactMethodsInternalCol,
            $callerGenderInternalCol,
            $regionCodeCol,
        ];

        $registrationData = GeneralSessionsEnum::SiteRegistrationFormData->getSession([]);
        $registrationData[$regionCodeCol] = CountryEnum::Iran->alpha2DigCode();

        $registrationDataKkeys = array_keys($registrationData);

        $betconstructClient = new BetconstructClient();

        foreach ($fillables as $fillable) {

            if (in_array($fillable, $registrationDataKkeys))
                $betconstructClient[$fillable] = $registrationData[$fillable];
        }

        $password = $registrationData[$passwordCol];
        $bcData = ClientModelEnum::convertDataToBcModel($betconstructClient->getAttributes());
        $bcData[ClientModelEnum::Password->name] = $password;

        $createClientResponse = ExternalAdminAPI::createClient($bcData);
        // $createClientResponse = ExternalAdminApiResponseTest::createClient(); // Do not clean this for fast action if need

        if ($createClientResponse->getStatus()->name === ApiStatusEnum::Success->name) {

            $betconstructClient = ClientModelEnum::fillModel($createClientResponse->getData(), $betconstructClient);

            $betconstructClient[ClientModelEnum::Password->dbName()] = $password;
            $betconstructClient[ClientModelEnum::MobileVerifiedAtInternal->dbName()] = $this->isMobileVerified() ? Carbon::now() : null;
            $betconstructId = $betconstructClient[ClientModelEnum::Id->dbName()];

            $betconstructSwarmClient = ClientSwarmModelEnum::fillModel($betconstructClient->getAttributes(), false);
            $betconstructSwarmClient[ClientSwarmModelEnum::Username->dbName()] = $registrationData[$loginCol];

            $loginResult = (new LoginBetconstructWebSocketController())->loginClient($betconstructClient, $betconstructSwarmClient, false);

            $betconstructClient[ClientModelEnum::Id->dbName()] = $betconstructId; // After login Id changes to zero

            if (JsonResponseHelper::isJsonResponseSuccess($loginResult)) {

                if ($this->isEmailVerified()) {

                    if ($user = UserBetconstruct::where(UsersTableEnum::Username->dbName(), $betconstructId)->first()) {

                        $user[UsersTableEnum::EmailVerifiedAt->dbName()] = Carbon::now();

                        $user->save();
                    }
                }

                $this->createReferralRecord($user->id);
                $this->resetRegistration();
                return redirect(AppSettingsEnum::CommunityRegistrationTargetLinkAfterComplete->getValue());
            } else {
                $this->moveStepBackward();
                return $this->redirectBackWithErrors(JsonResponseHelper::getJsonResponseMessage($loginResult));
            }
        } else {
            $this->moveStepBackward();
            return $this->redirectBackWithErrors($createClientResponse->getErrorMessage());
        }
    }

    /**
     * Create referral record
     *
     * @param  int $userId
     * @return void
     */
    private function createReferralRecord(int $userId): void
    {
        $referredBy = GeneralSessionsEnum::SiteRegistrationReferredBy->getSession();

        if (!is_null($referredBy)) {

            if ($referredUser = Referral::where(ReferralsTableEnum::ReferralId->dbName(), $referredBy)->first()) {

                if (!Referral::where(ReferralsTableEnum::UserId->dbName(), $userId)->exists()) {

                    Referral::Create([
                        ReferralsTableEnum::UserId->dbName() => $userId,
                        ReferralsTableEnum::ReferredBy->dbName() => $referredUser[ReferralsTableEnum::UserId->dbName()],
                    ]);
                }
            }
        }
    }

    /**
     * Final validatation of form data
     *
     * @return bool
     */
    private function validateFormData(): bool
    {
        $registrationData = GeneralSessionsEnum::SiteRegistrationFormData->getSession();
        $registrationDataKkeys = array_keys($registrationData);

        $steps = $this->getStepsList();

        // STEP_GET_MOBILE_NUMBER
        if (in_array(self::STEP_GET_MOBILE_NUMBER, $steps)) {

            if (!in_array(ClientModelEnum::MobilePhone->dbName(), $registrationDataKkeys))
                return false;
        }

        // STEP_VERIFY_MOBILE_NUMBER
        if (in_array(self::STEP_VERIFY_MOBILE_NUMBER, $steps)) {

            if (!in_array(self::IS_MOBILE_VERIFIED, $registrationDataKkeys))
                return false;

            if (!$registrationData[self::IS_MOBILE_VERIFIED])
                return false;
        }

        // STEP_GET_EMAIL
        if (in_array(self::STEP_GET_EMAIL, $steps)) {

            if (!in_array(ClientModelEnum::Email->dbName(), $registrationDataKkeys))
                return false;
        }

        // STEP_VERIFY_EMAIL
        if (in_array(self::STEP_VERIFY_EMAIL, $steps)) {

            if (!in_array(self::IS_EMAIL_VERIFIED, $registrationDataKkeys))
                return false;

            if (!$registrationData[self::IS_EMAIL_VERIFIED])
                return false;
        }

        return true;
    }

    /**
     * Check if the mobile was verified during registration
     *
     * @return bool
     */
    private function isMobileVerified(): bool
    {
        $registrationData = GeneralSessionsEnum::SiteRegistrationFormData->getSession();
        $registrationDataKkeys = array_keys($registrationData);

        $steps = $this->getStepsList();

        // STEP_VERIFY_MOBILE_NUMBER
        if (in_array(self::STEP_VERIFY_MOBILE_NUMBER, $steps)) {

            if (!in_array(self::IS_MOBILE_VERIFIED, $registrationDataKkeys))
                return false;

            if (!$registrationData[self::IS_MOBILE_VERIFIED])
                return false;
        }

        return true;
    }

    /**
     * Check if the email was verified during registration
     *
     * @return bool
     */
    private function isEmailVerified(): bool
    {
        $registrationData = GeneralSessionsEnum::SiteRegistrationFormData->getSession();
        $registrationDataKkeys = array_keys($registrationData);

        $steps = $this->getStepsList();

        // STEP_VERIFY_EMAIL
        if (in_array(self::STEP_VERIFY_EMAIL, $steps)) {

            if (!in_array(self::IS_EMAIL_VERIFIED, $registrationDataKkeys))
                return false;

            if (!$registrationData[self::IS_EMAIL_VERIFIED])
                return false;
        }

        return true;
    }

    /**
     * Update session form data
     *
     * @param  ?array $data
     * @return void
     */
    private function updateSessionFormData(?array $data): void
    {

        if (!empty($data)) {

            $formData = GeneralSessionsEnum::SiteRegistrationFormData->getSession([]);
            $formData = array_merge($formData, $data);
            GeneralSessionsEnum::SiteRegistrationFormData->setSession($formData);
        }
    }

    /**
     * Send verification SMS
     *
     * @param  ?string $mobileNumber
     * @return ?string string => error message, null => ok
     */
    private function sendVerificationSMS(?string $mobileNumber): ?string
    {
        if (empty($mobileNumber))
            return __('validation.required', ['attribute' => __('PagesContent_RegisaterationBetconstruct.form.mobile_phone.name')]);

        try {

            GeneralSessionsEnum::SiteRegistrationMobileNumber->setSession($mobileNumber);

            //Check if the client is already under verification
            if ($nextVerificationTime = $this->getNextVerificationTime(VerificationTypesEnum::Mobile, $mobileNumber))
                return $nextVerificationTime;

            $minutesToExpire = AppSettingsEnum::CommunityRegistrationMobileVerificationExpirationMinutes->getValue();
            $verification = VerificationTypesEnum::Mobile->makeVerificationRecord($minutesToExpire, null, null, $mobileNumber);

            $verificationText = AppSettingsEnum::CommunityRegistrationMobileVerificationText->getValue();
            $verificationText = str_replace("{verificationCode}", $verification[VerificationsTableEnum::Code->dbName()], $verificationText);

            $sendSmsResult = JustCallAPI::sendText($mobileNumber, $verificationText);

            // Do not remove the below line, this is a test for quick actions
            // $sendSmsResult = JustCallApiResponseTest::sendText($mobileNumber, $verificationText, TestResponseEnum::SendText_Fail_IncorrectNumber);

            if ($sendSmsResult->getStatus() == ApiStatusEnum::Success) {

                // Update session data
                $sessionVerificationAttemps = GeneralSessionsEnum::SiteRegistrationMobileVerificationAttemps->getSession(0);
                GeneralSessionsEnum::SiteRegistrationMobileVerificationAttemps->setSession($sessionVerificationAttemps + 1);
                GeneralSessionsEnum::SiteRegistrationMobileVerificationLastAttemp->setSession(Carbon::now()->toDateTimeString());

                return null;
            } else {
                $verification->delete();
                return $sendSmsResult->getErrorMessage();
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                'Issue during sending verification SMS'
            );

            $verification->delete();

            return __('general.error.unknown');
        }
    }

    /**
     * Send verification Email
     *
     * @param  ?string $email
     * @return ?string string => error message, null => ok
     */
    private function sendVerificationEmail(?string $email): ?string
    {
        if (empty($email))
            return __('validation.required', ['attribute' => __('PagesContent_RegisaterationBetconstruct.form.email.name')]);

        try {

            GeneralSessionsEnum::SiteRegistrationEmail->setSession($email);

            //Check if the client is already under verification
            if ($nextVerificationTime = $this->getNextVerificationTime(VerificationTypesEnum::Email, $email))
                return $nextVerificationTime;

            $minutesToExpire = AppSettingsEnum::CommunityRegistrationEmailVerificationExpirationMinutes->getValue();
            $verification = VerificationTypesEnum::Email->makeVerificationRecord($minutesToExpire, null, null, $email);

            $verificationText = AppSettingsEnum::CommunityRegistrationEmailVerificationText->getValue();
            $verificationText = str_replace("{verificationCode}", $verification[VerificationsTableEnum::Code->dbName()], $verificationText);

            Mail::to($email)->send(new EmailVerificationMail($verification->id));

            // Update session data
            $sessionVerificationAttemps = GeneralSessionsEnum::SiteRegistrationEmailVerificationAttemps->getSession(0);
            GeneralSessionsEnum::SiteRegistrationEmailVerificationAttemps->setSession($sessionVerificationAttemps + 1);
            GeneralSessionsEnum::SiteRegistrationEmailVerificationLastAttemp->setSession(Carbon::now()->toDateTimeString());

            return null;
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                'Issue during sending verification email'
            );

            $verification->delete();

            return __('general.error.unknown');
        }
    }

    /**
     * Get next verification time
     *
     * @param \App\Enums\Users\VerificationTypesEnum $verificationType
     * @param ?string $underVerificationValue mobileNumber|Email
     * @return ?string
     */
    private function getNextVerificationTime(VerificationTypesEnum $verificationType, ?string $underVerificationValue): ?string
    {

        if (empty($underVerificationValue))
            return null;

        $baseExpireMinutes = match ($verificationType) {

            VerificationTypesEnum::Mobile   => AppSettingsEnum::CommunityRegistrationMobileVerificationExpirationMinutes->getValue(),
            VerificationTypesEnum::Email    => AppSettingsEnum::CommunityRegistrationEmailVerificationExpirationMinutes->getValue(),
        };

        // Get attemps count from session
        $sessionVerificationAttemps = $this->getVerificationAttempsCount($verificationType, $underVerificationValue);

        // Last verification time
        $lastVerification = Verification::where(VerificationsTableEnum::NewValue->dbName(), $underVerificationValue)
            ->orderBy(VerificationsTableEnum::ValidUntil->dbName(), 'desc')
            ->orderBy(VerificationsTableEnum::Id->dbName(), 'desc')
            ->first();

        $verificationExpireTime = is_null($lastVerification) ? null : Carbon::parse($lastVerification[VerificationsTableEnum::ValidUntil->dbName()]);

        // Get attemps count from session
        $sessionVerificationAttemps = $this->getVerificationAttempsCount($verificationType, $underVerificationValue);

        // Reset session data if the last attempt was more than a day ago
        $sessionLastAttemp = match ($verificationType) {

            VerificationTypesEnum::Mobile   => GeneralSessionsEnum::SiteRegistrationMobileVerificationLastAttemp->getSession(),
            VerificationTypesEnum::Email    => GeneralSessionsEnum::SiteRegistrationEmailVerificationLastAttemp->getSession(),
        };

        if (is_null($sessionLastAttemp) && is_null($verificationExpireTime)) {
            return null; // There is no verification time for last attemp
        }

        $sessionLastAttemp = Carbon::parse($sessionLastAttemp);

        if (is_null($verificationExpireTime))
            $verificationExpireTime = $sessionLastAttemp->addMinutes($baseExpireMinutes);

        // Get attemps count from database
        $dbVerificationAttemps = Verification::where(VerificationsTableEnum::NewValue->dbName(), $underVerificationValue)
            ->where(TimestampsEnum::CreatedAt->dbName(), '>', Carbon::now()->subDay())
            ->count();

        $attempsCount = max($sessionVerificationAttemps, $dbVerificationAttemps);

        // Check if the daily attemp limit has been reached
        $dailyLimit = match ($verificationType) {

            VerificationTypesEnum::Mobile   => AppSettingsEnum::CommunityRegistrationMobileVerificationPerDay->getValue(),
            VerificationTypesEnum::Email    => AppSettingsEnum::CommunityRegistrationEmailVerificationPerDay->getValue(),
        };

        if ($attempsCount >= $dailyLimit) {

            $remainingMinutes = (24 * 60) - $baseExpireMinutes;
        } else {
            // Calculate next verification minutes base on attemps

            $attempCoefficient = match ($verificationType) {

                VerificationTypesEnum::Mobile   => AppSettingsEnum::CommunityRegistrationMobileVerificationExpirationMinutesCoefficient->getValue(),
                VerificationTypesEnum::Email    => AppSettingsEnum::CommunityRegistrationEmailVerificationExpirationMinutesCoefficient->getValue(),
            };

            $remainingMinutes = ($attempsCount * $attempCoefficient * $baseExpireMinutes) - $baseExpireMinutes;
            if ($remainingMinutes < 0)
                $remainingMinutes = 0;
        }

        $nextTime = $verificationExpireTime
            ->addMinutes($remainingMinutes);

        $timeDiff = (new CarbonTimeDiffForHuman($nextTime, Carbon::now()));

        if ($timeDiff->isSourceDatePassed())
            return null; // Next verification time has been reached


        $nextVerificationTime = $timeDiff->ignoreSuffixes()->getDiff();

        return match ($verificationType) {

            VerificationTypesEnum::Mobile   => __('PagesContent_RegisaterationBetconstruct.messages.verificationMobileNotReceived', ['remainingTime' => $nextVerificationTime]),
            VerificationTypesEnum::Email    => __('PagesContent_RegisaterationBetconstruct.messages.verificationEmailNotReceived', ['remainingTime' => $nextVerificationTime]),
        };
    }

    /**
     * Get mobile verification attemps count
     *
     * @param \App\Enums\Users\VerificationTypesEnum $verificationType
     * @param  ?string $underVerificationValue mobileNumber|Email
     * @return int
     */
    private function getVerificationAttempsCount(VerificationTypesEnum $verificationType, ?string $underVerificationValue): int
    {
        if (empty($underVerificationValue))
            return 0;

        // Reset session data if the last attempt was more than a day ago
        $sessionLastAttemp = match ($verificationType) {

            VerificationTypesEnum::Mobile   => GeneralSessionsEnum::SiteRegistrationMobileVerificationLastAttemp->getSession(),
            VerificationTypesEnum::Email    => GeneralSessionsEnum::SiteRegistrationEmailVerificationLastAttemp->getSession(),
        };

        if (!is_null($sessionLastAttemp)) {

            $sessionLastAttemp = Carbon::parse($sessionLastAttemp);

            if ($sessionLastAttemp < Carbon::now()->subDay()) {

                if ($verificationType == VerificationTypesEnum::Mobile) {

                    GeneralSessionsEnum::SiteRegistrationMobileVerificationAttemps->forgetSession();
                    GeneralSessionsEnum::SiteRegistrationMobileVerificationLastAttemp->forgetSession();
                } else if ($verificationType == VerificationTypesEnum::Email) {

                    GeneralSessionsEnum::SiteRegistrationEmailVerificationAttemps->forgetSession();
                    GeneralSessionsEnum::SiteRegistrationEmailVerificationLastAttemp->forgetSession();
                }
            }
        }

        // Get attemps count from session
        $sessionVerificationAttemps = match ($verificationType) {

            VerificationTypesEnum::Mobile   => GeneralSessionsEnum::SiteRegistrationMobileVerificationAttemps->getSession(0),
            VerificationTypesEnum::Email    => GeneralSessionsEnum::SiteRegistrationEmailVerificationAttemps->getSession(0),
        };

        // Get attemps count from database
        $dbVerificationAttemps = Verification::where(VerificationsTableEnum::NewValue->dbName(), $underVerificationValue)
            ->where(TimestampsEnum::CreatedAt->dbName(), '>', Carbon::now()->subDay())
            ->count();

        return max($sessionVerificationAttemps, $dbVerificationAttemps);
    }

    /**
     * Move registration step forward
     *
     * @return void
     */
    private function moveStepForward(): void
    {
        GeneralSessionsEnum::SiteRegistrationStep->setSession($this->getStep(self::NEXT_STEP));
    }

    /**
     * Move registration step backward
     *
     * @return void
     */
    private function moveStepBackward(): void
    {
        GeneralSessionsEnum::SiteRegistrationStep->setSession($this->getStep(self::PREVIOUS_STEP));
    }

    /**
     * Redirect back with errors
     *
     * @param  string|array $error
     * @return Illuminate\Routing\Redirector|Illuminate\Http\RedirectResponse
     */
    private function redirectBackWithErrors(string|array $error): Redirector|RedirectResponse
    {
        $rawData = GeneralSessionsEnum::SiteRegistrationRawData->getSession([]);
        return redirect()->back()->withInput($rawData)->withErrors($error);
    }

    /**
     * Get step by key
     *
     * @param  int $directionKey CURRENT_STEP | NEXT_STEP | PREVIOUS_STEP
     * @return ?string
     */
    private function getStep(int $directionKey = self::CURRENT_STEP): ?string
    {
        $directionKeys = [
            self::CURRENT_STEP,
            self::NEXT_STEP,
            self::PREVIOUS_STEP,
        ];

        if (!in_array($directionKey, $directionKeys))
            return null;

        $steps = $this->getStepsList();

        $currentStep = GeneralSessionsEnum::SiteRegistrationStep->getSession($steps[0]);

        if (!in_array($currentStep, $steps))
            $currentStep = $steps[0];

        if ($directionKey == self::CURRENT_STEP)
            return $currentStep;

        $stepIndex = ArrayHelper::search($currentStep, $steps);

        if ($stepIndex !== false) {

            if ($directionKey == self::NEXT_STEP)
                $stepIndex++;
            else if ($directionKey == self::PREVIOUS_STEP)
                $stepIndex--;

            if ($stepIndex < 0 || $stepIndex > (count($steps) - 1))
                return null;

            return $steps[$stepIndex];
        }

        return null;
    }

    /**
     * Get steps list
     *
     * @return array
     */
    private function getStepsList(): array
    {
        // Put the steps in order

        $steps = [];

        $registrationFields = AppSettingsEnum::CommunityRegistrationFields->getValue();
        $registrationFields = is_null($registrationFields) ? [] : json_decode($registrationFields);

        $isMobilePhoneRequired = in_array(ClientRegistrationAvailabelFieldsEnum::MobilePhone->value, $registrationFields);

        // STEP_GET_MOBILE_NUMBER
        if ($isMobilePhoneRequired)
            array_push($steps, self::STEP_GET_MOBILE_NUMBER);

        // STEP_VERIFY_MOBILE_NUMBER
        if ($isMobilePhoneRequired && AppSettingsEnum::CommunityRegistrationMobileVerificationIsRequired->getValue())
            array_push($steps, self::STEP_VERIFY_MOBILE_NUMBER);

        $isEmailPhoneRequired = in_array(ClientRegistrationAvailabelFieldsEnum::Email->value, $registrationFields);

        // STEP_GET_EMAIL
        if ($isEmailPhoneRequired)
            array_push($steps, self::STEP_GET_EMAIL);

        // STEP_VERIFY_EMAIL
        if ($isEmailPhoneRequired && AppSettingsEnum::CommunityRegistrationEmailVerificationIsRequired->getValue())
            array_push($steps, self::STEP_VERIFY_EMAIL);

        // STEP_ACCOUNT_DATA
        array_push($steps, self::STEP_ACCOUNT_DATA);

        // STEP_FURTHER_INFORMATION
        if (!empty(ClientRegistrationAvailabelFieldsEnum::getFurtherInformationItems()))
            array_push($steps, self::STEP_FURTHER_INFORMATION);

        // STEP_SUBMIT
        array_push($steps, self::STEP_SUBMIT);

        return $steps;
    }

    /**
     * Check if the step is the first step of registration
     *
     * @param  ?string $stepName -optianl- default: CURRENT_STEP
     * @return bool
     */
    private function isFristStep(?string $stepName = null): bool
    {
        $steps = $this->getStepsList();

        if (empty($stepName))
            $stepName = $this->getStep(self::CURRENT_STEP);

        return $steps[0] == $stepName;
    }

    /**
     * Check if the step is the last step of registration
     *
     * @param  ?string $stepName -optianl- default: CURRENT_STEP
     * @return bool
     */
    private function isLastStep(?string $stepName = null): bool
    {
        $steps = $this->getStepsList();

        if (empty($stepName))
            $stepName = $this->getStep(self::CURRENT_STEP);

        return $steps[count($steps) - 2] == $stepName;
    }

    /**
     * Get steps progress list
     *
     * @return array
     */
    private function getStepsProgressList(): array
    {
        $steps = $this->getStepsList();

        $currentStep = $this->getStep(self::CURRENT_STEP);

        $progressList = [];

        $isStepPassed = true;

        foreach ($steps as $stepName) {

            if ($stepName == self::STEP_SUBMIT)
                break;

            if ($stepName == $currentStep)
                $isStepPassed = false;

            $icon = match ($stepName) {

                self::STEP_GET_MOBILE_NUMBER    => 'fa-solid fa-mobile-screen-button',
                self::STEP_VERIFY_MOBILE_NUMBER => 'fa-solid fa-mobile-signal-out',
                self::STEP_GET_EMAIL            => 'fa-solid fa-envelope',
                self::STEP_VERIFY_EMAIL         => 'fa-solid fa-envelope-circle-check',
                self::STEP_ACCOUNT_DATA         => 'fa fa-passport',
                self::STEP_FURTHER_INFORMATION  => 'fa-solid fa-square-info',

                default => ""
            };

            $progressList[$stepName] = [
                'isPassed' => $isStepPassed,
                'displayName' => __('PagesContent_RegisaterationBetconstruct.tab.' . $stepName . '.title'),
                'icon' => $icon,
            ];
        }
        return $progressList;
    }

    /**
     * Reset registration
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    private function resetRegistration(): Redirector|RedirectResponse
    {
        GeneralSessionsEnum::forgetRegistrationSessions();
        return redirect(SitePublicRoutesEnum::RegisterBetconstruct->url());
    }

    /********************* API Requests *********************/

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
        return JsonResponseHelper::successResponse($data, 'Success');
    }

    /********************* API Requests END *********************/
}
