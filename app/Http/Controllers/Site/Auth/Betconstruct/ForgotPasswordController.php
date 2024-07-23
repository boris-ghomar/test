<?php

namespace App\Http\Controllers\Site\Auth\Betconstruct;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Database\Tables\VerificationsTableEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Session\GeneralSessionsEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\Enums\SystemReserved\ClientCategoryReservedEnum;
use App\Enums\Users\PasswordRecoveryMethodEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Enums\Users\UsersTypesEnum;
use App\Enums\Users\VerificationTypesEnum;
use App\HHH_Library\general\php\CarbonTimeDiffForHuman;
use App\HHH_Library\general\php\DropdownListCreater;
use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\general\php\traits\Enums\EnumSession;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ApiConfigEnum as ExternalAdminApiConfigEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\ExternalAdminAPI;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\HHH_Library\ThisApp\API\JustCall\JustCallAPI;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\Auth\ForgotPasswordAttempRequest;
use App\Http\Requests\Site\Auth\ForgotPasswordResetPasswordAttempRequest;
use App\Http\Requests\Site\Auth\ForgotPasswordVerificationAttempRequest;
use App\Mail\EmailVerificationMail;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use App\Models\General\Verification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{

    use EnumSession;

    /**
     * Display forgot password form.
     */
    public function index()
    {
        if (!AppSettingsEnum::CommunityPasswordRecoveryIsActive->getValue(false))
            return redirect(SitePublicRoutesEnum::MainPage->url());

        $passwordRecoveryMethods = $this->getAvailablePasswordRecoveryMethods();

        if (empty($passwordRecoveryMethods))
            return redirect(SitePublicRoutesEnum::MainPage->url());
        else if (count($passwordRecoveryMethods) == 1)
            return redirect(SitePublicRoutesEnum::ForgotPasswordRecoveryMethod->url(['RecoveryMethod' => $passwordRecoveryMethods[0]]));

        $recoveryMethods = [];
        foreach ($passwordRecoveryMethods as $passwordRecoveryMethod) {

            if ($passwordRecoveryMethodCase = PasswordRecoveryMethodEnum::getCase($passwordRecoveryMethod))
                $recoveryMethods[$passwordRecoveryMethodCase->name] = $passwordRecoveryMethodCase->translate();
        }

        $recoveryMethods = DropdownListCreater::makeByArray($recoveryMethods)
            ->get();

        $data = [
            'recoveryMethods' => $recoveryMethods,
            'defaultRecoveryMethod' => AppSettingsEnum::CommunityPasswordRecoveryDefaultMethod->getValue(),
        ];

        return view('hhh.Site.pages.auth.Betconstruct.ForgotPassword.index', $data);
    }

    /**
     * Recovery method page
     *
     * @param  mixed $request
     * @return Redirect|view
     */
    public function recoveryMethodPage(Request $request)
    {
        if (!AppSettingsEnum::CommunityPasswordRecoveryIsActive->getValue(false))
            return redirect(SitePublicRoutesEnum::MainPage->url());

        $recoveryMethod = $request->input('RecoveryMethod');

        if (!in_array($recoveryMethod, self::getAvailablePasswordRecoveryMethods()))
            return redirect(SitePublicRoutesEnum::ForgotPasswordBetconstruct->url());

        $view = match ($recoveryMethod) {

            PasswordRecoveryMethodEnum::Email->name     => view('hhh.Site.pages.auth.Betconstruct.ForgotPassword.Methods.forgot-password-email'),
            PasswordRecoveryMethodEnum::Mobile->name    => view('hhh.Site.pages.auth.Betconstruct.ForgotPassword.Methods.forgot-password-mobile'),

            default => null
        };

        if (is_null($view))
            return redirect(SitePublicRoutesEnum::ForgotPasswordBetconstruct->url());

        GeneralSessionsEnum::SiteRecoveryPasswordMethod->setSession($recoveryMethod);

        return $view;
    }

    /**
     * Handle forgot password request attempt.
     *
     * @param  \App\Http\Requests\Site\Auth\ForgotPasswordAttempRequest $request
     * @return Redirect|View
     */
    public function attempt(ForgotPasswordAttempRequest $request)
    {
        if (!AppSettingsEnum::CommunityPasswordRecoveryIsActive->getValue(false))
            return redirect(SitePublicRoutesEnum::MainPage->url());

        $recoveryMethod = GeneralSessionsEnum::SiteRecoveryPasswordMethod->getSession();
        if (!in_array($recoveryMethod, self::getAvailablePasswordRecoveryMethods()))
            return redirect(SitePublicRoutesEnum::ForgotPasswordBetconstruct->url());

        # Search client
        $client = null;

        if ($recoveryMethod == PasswordRecoveryMethodEnum::Email->name) {

            $verifiable = $request->input(PasswordRecoveryMethodEnum::Email->name);
            $client = BetconstructClient::where(ClientModelEnum::Email->dbName(), $verifiable)
                ->first();

            if (is_null($client)) {
                $client = $this->fetchClientByEmail($verifiable);
            }
        } else if ($recoveryMethod == PasswordRecoveryMethodEnum::Mobile->name) {

            $verifiable = $request->input(PasswordRecoveryMethodEnum::Mobile->name);

            $client = $this->findClientByMobile($verifiable);
        }

        if (is_null($client))
            return redirect()->back()->withInput()->withErrors(__('auth_site.custom.ForgotPasswordForm.errors.accountNotFound'));

        GeneralSessionsEnum::SiteRecoveryPasswordUserId->setSession($client[ClientModelEnum::UserId->dbName()]);

        $verificationCodeSendResult = $this->sendVerificationCode($client, $recoveryMethod, $verifiable);

        if (is_null($verificationCodeSendResult))
            return redirect(SitePublicRoutesEnum::ForgotPasswordVerifiyPage->url())->withErrors([]);
        else
            return redirect()->back()->withInput()->withErrors([$verificationCodeSendResult]);
    }

    /**
     * Verification page
     *
     * @return Redirect|view
     */
    public function verificationPage()
    {
        $recoveryMethod = GeneralSessionsEnum::SiteRecoveryPasswordMethod->getSession();
        $verifiable = GeneralSessionsEnum::SiteRecoveryPasswordVerifiable->getSession();

        $forgetPasswordMainPageRedirect = redirect(SitePublicRoutesEnum::ForgotPasswordBetconstruct->url());

        if (is_null($recoveryMethod))
            return $forgetPasswordMainPageRedirect;

        $verificationType = match ($recoveryMethod) {

            PasswordRecoveryMethodEnum::Email->name => VerificationTypesEnum::Email,
            PasswordRecoveryMethodEnum::Mobile->name => VerificationTypesEnum::Mobile,

            default => null
        };

        if (is_null($verificationType))
            return $forgetPasswordMainPageRedirect;

        $nextVerificationTime = $this->getNextVerificationTime($verificationType, $verifiable);

        if (is_null($nextVerificationTime))
            return $forgetPasswordMainPageRedirect;

        $data = [
            'nextVerificationTime' => $nextVerificationTime,
        ];

        return view('hhh.Site.pages.auth.Betconstruct.ForgotPassword.forgot-password-verification', $data);
    }

    /**
     * Account verification attemp
     *
     * @param  \App\Http\Requests\Site\Auth\ForgotPasswordVerificationAttempRequest $request
     * @return void
     */
    public function verificationAttemp(ForgotPasswordVerificationAttempRequest $request)
    {

        $accessDenied = redirect()->back()->withInput()->withErrors(__('auth_site.custom.AccessDenied'));

        $verificationCode = $request->input('VerificationCode');

        $userId = GeneralSessionsEnum::SiteRecoveryPasswordUserId->getSession();
        $recoveryMethod = GeneralSessionsEnum::SiteRecoveryPasswordMethod->getSession();
        $verifiable = GeneralSessionsEnum::SiteRecoveryPasswordVerifiable->getSession();

        $user = UserBetconstruct::find($userId);
        if (is_null($user))
            return $accessDenied;

        $verificationType = match ($recoveryMethod) {

            PasswordRecoveryMethodEnum::Email->name => VerificationTypesEnum::Email,
            PasswordRecoveryMethodEnum::Mobile->name => VerificationTypesEnum::Mobile,

            default => null
        };

        if (is_null($verificationType))
            return $accessDenied;

        $verification = $verificationType->getVerification($user, $verifiable);

        $verificationFailed = redirect()->back()->withInput()->withErrors(__('auth_site.custom.ForgotPasswordForm.errors.verificationFailed'));;

        if (is_null($verification))
            return $verificationFailed;

        if ($verification[VerificationsTableEnum::Code->dbName()] != $verificationCode)
            return $verificationFailed;
        else {

            $resetPasswordHash = json_encode([
                'userId' => $userId,
                'verificationId' => $verification[VerificationsTableEnum::Id->dbName()],
                'verifiable' => $verifiable,
                'recoveryMethod' => $recoveryMethod,
            ]);

            GeneralSessionsEnum::SiteRecoveryPasswordResetPasswordHash->setSession(Crypt::encrypt($resetPasswordHash));

            return redirect(SitePublicRoutesEnum::ForgotPasswordResetPasswordAttemp->url())->withErrors([]);
        }
    }

    /**
     * Reset password page
     *
     * @return Redirect
     */
    public function resetPasswordPage()
    {

        $forgetPasswordMainPageRedirect = redirect(SitePublicRoutesEnum::ForgotPasswordBetconstruct->url());

        # Hash validation
        $resetPasswordHash = GeneralSessionsEnum::SiteRecoveryPasswordResetPasswordHash->getSession();

        if (empty($resetPasswordHash))
            return $forgetPasswordMainPageRedirect;

        $resetPasswordHash = Crypt::decrypt($resetPasswordHash);
        $resetPasswordHash = json_decode($resetPasswordHash, true);

        if ($resetPasswordHash['userId'] != GeneralSessionsEnum::SiteRecoveryPasswordUserId->getSession())
            return $forgetPasswordMainPageRedirect;

        if ($resetPasswordHash['verifiable'] != GeneralSessionsEnum::SiteRecoveryPasswordVerifiable->getSession())
            return $forgetPasswordMainPageRedirect;

        if ($resetPasswordHash['recoveryMethod'] != GeneralSessionsEnum::SiteRecoveryPasswordMethod->getSession())
            return $forgetPasswordMainPageRedirect;

        $verification = Verification::find($resetPasswordHash['verificationId']);
        if (is_null($verification))
            return $forgetPasswordMainPageRedirect;

        if ($verification[VerificationsTableEnum::ValidUntil->dbName()] > Carbon::now()) {
            // Expiration of verification code

            $verification[VerificationsTableEnum::ValidUntil->dbName()] = Carbon::now();
            $verification->save();
        }

        # Update verified_at records
        if ($userBetconstruct = UserBetconstruct::find(GeneralSessionsEnum::SiteRecoveryPasswordUserId->getSession())) {

            $recoveryMethod = GeneralSessionsEnum::SiteRecoveryPasswordMethod->getSession();

            if ($recoveryMethod == PasswordRecoveryMethodEnum::Email->name) {

                $userBetconstruct[UsersTableEnum::EmailVerifiedAt->dbName()] = Carbon::now();
                $userBetconstruct->save();
            } else if ($recoveryMethod == PasswordRecoveryMethodEnum::Mobile->name) {

                $betconstructClient = $userBetconstruct->betconstructClient;
                $betconstructClient[ClientModelEnum::MobileVerifiedAtInternal->dbName()] = Carbon::now();
                $betconstructClient->save();
            }
        }


        return view('hhh.Site.pages.auth.Betconstruct.ForgotPassword.reset-password');
    }

    /**
     * Reset password attemp
     *
     * @param  \App\Http\Requests\Site\Auth\ForgotPasswordResetPasswordAttempRequest $request
     * @return redirect
     */
    public function resetPasswordAttemp(ForgotPasswordResetPasswordAttempRequest $request)
    {
        $newPassword = $request->input('password');

        $userId = GeneralSessionsEnum::SiteRecoveryPasswordUserId->getSession();
        $userBetconstruct = UserBetconstruct::find($userId);

        if (is_null($userBetconstruct)) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                sprintf('Requested user ID: %s', $userId),
                'User not found in reset password attempt!'
            );
            return redirect()->back()->withInput()->withErrors([__('auth_site.custom.ResetPasswordForm.UserNotFound')]);
        }

        $betconstructClient = $userBetconstruct->betconstructClient;
        $betconstructClient[ClientModelEnum::Password->dbName()] = $newPassword;


        $updateClientResponse = ExternalAdminAPI::changeClientPassword($betconstructClient[ClientModelEnum::Id->dbName()], $betconstructClient[ClientModelEnum::Login->dbName()], $newPassword);

        if ($updateClientResponse->getStatus()->name === ApiStatusEnum::Success->name) {

            $userBetconstruct[UsersTableEnum::Password->dbName()] = Hash::make($newPassword);
            $userBetconstruct->save();

            $betconstructClient = ClientModelEnum::fillModel($updateClientResponse->getData(), $betconstructClient);
            $betconstructClient[ClientModelEnum::Password->dbName()] = Crypt::encrypt($newPassword);
            $betconstructClient->save();

            EnumSession::logoutUserFromAllDevices($userBetconstruct);

            GeneralSessionsEnum::forgetRecoveryPasswordSessions();

            return redirect(SitePublicRoutesEnum::defaultLogin()->url())
                ->withInput([
                    'username' => $betconstructClient[ClientModelEnum::Login->dbName()],
                    'password' => $newPassword,
                ])
                ->with('success', trans('auth_site.custom.ForgotPasswordForm.messages.successfullyReset'));
        } else {
            $errorMessage = $updateClientResponse->getErrorMessage();
            return redirect()->back()->withInput()->withErrors([$errorMessage]);
        }
    }

    /**
     * Get available password recovery methods
     *
     * @return array
     */
    public static function getAvailablePasswordRecoveryMethods(): array
    {
        $passwordRecoveryMethods = AppSettingsEnum::CommunityPasswordRecoveryMethods->getValue();
        return is_null($passwordRecoveryMethods) ? [] : json_decode($passwordRecoveryMethods);
    }

    /**
     * Find client by mobile
     *
     * @param  ?string $mobile
     * @return null|App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient
     */
    private function findClientByMobile(?string $mobile): ?BetconstructClient
    {
        if (empty($mobile))
            return null;

        $client = BetconstructClient::where(ClientModelEnum::MobilePhone->dbName(), $mobile)
            ->first();

        // Betconstruct saves the mobile phone in phone field
        if (is_null($client))
            $client = BetconstructClient::where(ClientModelEnum::Phone->dbName(), $mobile)
                ->first();

        if (is_null($client)) {
            $client = $this->fetchClientByMobile($mobile);
        }

        // Try to find client with other formats of mobile number
        if (is_null($client)) {

            // Try to find client with "+" instead of "00" (Exmp: +1...)
            if (Str::of($mobile)->startsWith("00")) {

                $mobile = Str::of($mobile)->replaceFirst("00", "+")->toString();
                return $this->findClientByMobile($mobile);
            }
            // Try to find client without "+" and "00" (Exmp: 1...)
            else if (Str::of($mobile)->startsWith("+")) {

                $mobile = Str::of($mobile)->replaceFirst("+", "")->toString();
                return $this->findClientByMobile($mobile);
            }
            // Try to find client without country code (Exmp: 0...)
            else if (Str::of($mobile)->startsWith("98")) {

                $mobile = Str::of($mobile)->replaceFirst("98", "0")->toString();

                if (Str::of($mobile)->startsWith("00"))
                    $mobile = Str::of($mobile)->replaceFirst("00", "0")->toString();

                return $this->findClientByMobile($mobile);
            }
            // Try to find client without country code and first 0 (Exmp: ...)
            else if (Str::of($mobile)->startsWith("0")) {

                $mobile = Str::of($mobile)->replaceFirst("0", "")->toString();
                return $this->findClientByMobile($mobile);
            }
        }

        return $client;
    }

    /**
     * fetchClientByEmail
     *
     * @param  ?string $email
     * @return null|App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient
     */
    private function fetchClientByEmail(?string $email): ?BetconstructClient
    {
        if (empty($email))
            return null;

        $clientsResponse = ExternalAdminAPI::getClientByEmail($email);
        return $this->registerClient($clientsResponse);
    }

    /**
     * fetchClientByMobile
     *
     * @param  ?string $mobile
     * @return null|App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient
     */
    private function fetchClientByMobile(?string $mobile): ?BetconstructClient
    {
        if (empty($mobile))
            return null;

        $clientsResponse = ExternalAdminAPI::getClientByMobilePhone($mobile);

        if ($clientsResponse->getStatus()->name === ApiStatusEnum::Success->name) {
            return $this->registerClient($clientsResponse);
        }

        // Betconstruct saves the mobile phone in phone field
        $clientsResponse = ExternalAdminAPI::getClientByPhoneNumber($mobile);
        return $this->registerClient($clientsResponse);
    }

    /**
     * Register not exists client
     *
     * @param  null|App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Requests\GetClientsRequest
     * @return null|App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient
     */
    private function registerClient(?GetClientsRequest $clientsResponse): ?BetconstructClient
    {

        if (is_null($clientsResponse))
            return null;

        try {

            if ($clientsResponse->getStatus()->name === ApiStatusEnum::Success->name) {

                $clientData = (new Collection($clientsResponse->getData()))->first();

                if (is_null($clientData))
                    return null;

                $betconstructClient = ClientModelEnum::fillModel($clientData);

                // Betconstruct Client ID, in users table saved in username
                $userBetconstruct = UserBetconstruct::where(UsersTableEnum::Username->dbName(), $betconstructClient[ClientModelEnum::Id->dbName()])
                    ->first();

                $usersTabelEmail = sprintf("%s@%s", $betconstructClient[ClientModelEnum::Id->dbName()], ExternalAdminApiConfigEnum::PartnerId->getValue());
                $password = Str::random(20); // Temporarly random password
                $clientCategory = ClientCategoryReservedEnum::NormalUser->model(); // Temporarly role

                $betconstructId = $betconstructClient[ClientModelEnum::Id->dbName()];

                if (is_null($userBetconstruct)) {
                    // new user
                    $userBetconstruct = new UserBetconstruct();

                    $userBetconstruct->forceFill([
                        UsersTableEnum::Username->dbName()  => $betconstructId,
                        UsersTableEnum::Email->dbName()     => $usersTabelEmail,
                        UsersTableEnum::Password->dbName()  => Hash::make($password),
                        UsersTableEnum::Type->dbName()      => UsersTypesEnum::BetconstructClient->name,
                        UsersTableEnum::RoleId->dbName()    => $clientCategory->id,
                        UsersTableEnum::Status->dbName()    => UsersStatusEnum::Active->name,
                    ]);
                } else {
                    // Existing user

                    $userBetconstruct->forceFill([
                        UsersTableEnum::Password->dbName()  => Hash::make($password),
                        UsersTableEnum::Email->dbName()     => $usersTabelEmail,
                        UsersTableEnum::RoleId->dbName()    => $clientCategory->id,
                    ]);
                }

                if ($userBetconstruct->save()) {

                    $userId = $userBetconstruct[UsersTableEnum::Id->dbName()];

                    $betconstructClient[ClientModelEnum::UserId->dbName()] = $userId;

                    $betconstructClient->save();
                    $betconstructClient[ClientModelEnum::Id->dbName()] = $betconstructId; // After save id chages to 0

                    return $betconstructClient;
                }
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Failed to save Betconstruct client."
            );
        }


        return null;
    }

    /**
     * Send verification code
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $client
     * @param  string $recoveryMethod
     * @param  string|int $verifiable
     * @return ?string string: error, null: success
     */
    private function sendVerificationCode(BetconstructClient $client, string $recoveryMethod, string|int $verifiable): ?string
    {
        GeneralSessionsEnum::SiteRecoveryPasswordVerifiable->setSession($verifiable);

        $user = $client->user;

        // Email
        if ($recoveryMethod == PasswordRecoveryMethodEnum::Email->name) {

            try {

                //Check if the client is already under verification
                if (!is_null($this->getNextVerificationTime(VerificationTypesEnum::Email, $verifiable)))
                    return null;

                $minutesToExpire = AppSettingsEnum::CommunityPasswordRecoveryEmailVerificationExpirationMinutes->getValue();
                $verification = VerificationTypesEnum::Email->makeVerificationRecord($minutesToExpire, $user, $verifiable, $verifiable);

                Mail::to($verifiable)->send(new EmailVerificationMail($verification->id));

                // Update session data
                $sessionVerificationAttemps = GeneralSessionsEnum::SiteRecoveryPasswordVerificationAttempsEmail->getSession(0);
                GeneralSessionsEnum::SiteRecoveryPasswordVerificationAttempsEmail->setSession($sessionVerificationAttemps + 1);
                GeneralSessionsEnum::SiteRecoveryPasswordVerificationLastAttempEmail->setSession(Carbon::now()->toDateTimeString());

                return null;
            } catch (\Throwable $th) {

                LogCreator::createLogError(
                    __CLASS__,
                    __FUNCTION__,
                    $th->getMessage(),
                    'Issue during sending verification email'
                );

                if (!is_null($verification))
                    $verification->delete();

                return __('general.error.unknown');
            }
        }
        // Mobile
        else if ($recoveryMethod == PasswordRecoveryMethodEnum::Mobile->name) {

            try {
                //Check if the client is already under verification
                if (!is_null($this->getNextVerificationTime(VerificationTypesEnum::Mobile, $verifiable)))
                    return null;

                $minutesToExpire = AppSettingsEnum::CommunityPasswordRecoveryMobileVerificationExpirationMinutes->getValue();
                $verification = VerificationTypesEnum::Mobile->makeVerificationRecord($minutesToExpire, $user, $verifiable, $verifiable);

                $verificationText = AppSettingsEnum::CommunityPasswordRecoveryMobileVerificationText->getValue();
                $verificationText = str_replace("{verificationCode}", $verification[VerificationsTableEnum::Code->dbName()], $verificationText);

                $sendSmsResult = JustCallAPI::sendText($verifiable, $verificationText);

                // Do not remove the below line, this is a test for quick actions
                // $sendSmsResult = JustCallApiResponseTest::sendText($verifiable, $verificationText, TestResponseEnum::SendText_Fail_IncorrectNumber);

                if ($sendSmsResult->getStatus() == ApiStatusEnum::Success) {

                    // Update session data
                    $sessionVerificationAttemps = GeneralSessionsEnum::SiteRecoveryPasswordVerificationAttempsMobile->getSession(0);
                    GeneralSessionsEnum::SiteRecoveryPasswordVerificationAttempsMobile->setSession($sessionVerificationAttemps + 1);
                    GeneralSessionsEnum::SiteRecoveryPasswordVerificationLastAttempMobile->setSession(Carbon::now()->toDateTimeString());

                    return null;
                } else {
                    if (!is_null($verification))
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

                if (!is_null($verification))
                    $verification->delete();

                return __('general.error.unknown');
            }
        } else {
            return __('auth_site.custom.ForgotPasswordForm.errors.invalidPasswordRecoveryMethod');
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

            VerificationTypesEnum::Mobile   => AppSettingsEnum::CommunityPasswordRecoveryMobileVerificationExpirationMinutes->getValue(),
            VerificationTypesEnum::Email    => AppSettingsEnum::CommunityPasswordRecoveryEmailVerificationExpirationMinutes->getValue(),
        };

        // Last verification time
        $lastVerification = Verification::where(VerificationsTableEnum::NewValue->dbName(), $underVerificationValue)
            ->orderBy(VerificationsTableEnum::ValidUntil->dbName(), 'desc')
            ->orderBy(VerificationsTableEnum::Id->dbName(), 'desc')
            ->first();

        $verificationExpireTime = is_null($lastVerification) ? null : Carbon::parse($lastVerification[VerificationsTableEnum::ValidUntil->dbName()]);

        // Reset session data if the last attempt was more than a day ago
        $sessionLastAttemp = match ($verificationType) {

            VerificationTypesEnum::Mobile   => GeneralSessionsEnum::SiteRecoveryPasswordVerificationLastAttempMobile->getSession(),
            VerificationTypesEnum::Email    => GeneralSessionsEnum::SiteRecoveryPasswordVerificationLastAttempEmail->getSession(),
        };

        if (is_null($sessionLastAttemp) && is_null($verificationExpireTime)) {
            return null; // There is no verification time for last attemp
        }

        $sessionLastAttemp = Carbon::parse($sessionLastAttemp);

        if (is_null($verificationExpireTime))
            $verificationExpireTime = $sessionLastAttemp->addMinutes($baseExpireMinutes);

        // Get attemps count
        $attempsCount = $this->getVerificationAttempsCount($verificationType, $underVerificationValue);

        // Check if the daily attemp limit has been reached
        $dailyLimit = match ($verificationType) {

            VerificationTypesEnum::Mobile   => AppSettingsEnum::CommunityPasswordRecoveryMobileVerificationPerDay->getValue(),
            VerificationTypesEnum::Email    => AppSettingsEnum::CommunityPasswordRecoveryEmailVerificationPerDay->getValue(),
        };

        if ($attempsCount >= $dailyLimit) {

            $remainingMinutes = (24 * 60) - $baseExpireMinutes;

            $nextTime = $verificationExpireTime
                ->addMinutes($remainingMinutes);
        } else {
            // Calculate next verification minutes base on attemps

            $nextTime = $verificationExpireTime;
        }

        $timeDiff = (new CarbonTimeDiffForHuman($nextTime, Carbon::now()));

        if ($timeDiff->isSourceDatePassed())
            return null; // Next verification time has been reached


        $nextVerificationTime = $timeDiff->ignoreSuffixes()->getDiff();

        $messageData = [
            'remainingTime' => $nextVerificationTime,
            'verifiable' => $underVerificationValue,
        ];

        return match ($verificationType) {

            VerificationTypesEnum::Mobile   => __('auth_site.custom.ForgotPasswordForm.errors.verificationMobileNotReceived', $messageData),
            VerificationTypesEnum::Email    => __('auth_site.custom.ForgotPasswordForm.errors.verificationEmailNotReceived', $messageData),
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

            VerificationTypesEnum::Mobile   => GeneralSessionsEnum::SiteRecoveryPasswordVerificationLastAttempMobile->getSession(),
            VerificationTypesEnum::Email    => GeneralSessionsEnum::SiteRecoveryPasswordVerificationLastAttempEmail->getSession(),
        };

        if (!is_null($sessionLastAttemp)) {

            $sessionLastAttemp = Carbon::parse($sessionLastAttemp);

            if ($sessionLastAttemp < Carbon::now()->subDay()) {

                if ($verificationType == VerificationTypesEnum::Mobile) {

                    GeneralSessionsEnum::SiteRecoveryPasswordVerificationAttempsMobile->forgetSession();
                    GeneralSessionsEnum::SiteRecoveryPasswordVerificationLastAttempMobile->forgetSession();
                } else if ($verificationType == VerificationTypesEnum::Email) {

                    GeneralSessionsEnum::SiteRecoveryPasswordVerificationAttempsEmail->forgetSession();
                    GeneralSessionsEnum::SiteRecoveryPasswordVerificationLastAttempEmail->forgetSession();
                }
            }
        }

        // Get attemps count from session
        $sessionVerificationAttemps = match ($verificationType) {

            VerificationTypesEnum::Mobile   => GeneralSessionsEnum::SiteRecoveryPasswordVerificationAttempsMobile->getSession(0),
            VerificationTypesEnum::Email    => GeneralSessionsEnum::SiteRecoveryPasswordVerificationAttempsEmail->getSession(0),
        };

        // Get attemps count from database
        $dbVerificationAttemps = Verification::where(VerificationsTableEnum::NewValue->dbName(), $underVerificationValue)
            ->where(TimestampsEnum::CreatedAt->dbName(), '>', Carbon::now()->subDay())
            ->count();

        return max($sessionVerificationAttemps, $dbVerificationAttemps);
    }
}
