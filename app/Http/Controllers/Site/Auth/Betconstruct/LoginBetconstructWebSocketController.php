<?php

namespace App\Http\Controllers\Site\Auth\Betconstruct;

use App\Enums\Database\Defaults\TimestampsEnum;
use App\Enums\Database\Tables\ClientCategoryMapsTableEnum;
use App\Enums\Database\Tables\RolesTableEnum;
use App\Enums\Database\Tables\UsersTableEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Session\GeneralSessionsEnum;
use App\Enums\SystemReserved\ClientCategoryReservedEnum;
use App\Enums\Users\ClientCategoryMapTypesEnum;
use App\Enums\Users\UsersStatusEnum;
use App\Enums\Users\UsersTypesEnum;
use App\HHH_Library\general\php\Enums\ApiStatusEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonHelper;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ApiConfigEnum as ExternalAdminApiConfigEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\ExternalAdminAPI;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Helpers\Tests\ApiResponseTest as ExternalAdminApiResponseTest; // Do not delete this for quick access when you need to test
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\RequestParamsEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\SessionEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Tests\ApiResponseTest as SwarmApiResponseTest; // Do not delete this for quick access when you need to test
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\Auth\LoginAttemptRequest;
use App\Models\BackOffice\ClientsManagement\ClientCategory;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Enums\ClientSwarmModelEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\GetUserRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Requests\LoginRequest;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Helpers\Tests\Enums\TestResponseEnum as EnumsTestResponseEnum;
use App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Models\BetconstructSwarmClient;
use App\Models\BackOffice\ClientsManagement\ClientCategoryMap;
use App\Models\BackOffice\ClientsManagement\UserBetconstruct;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class LoginBetconstructWebSocketController extends Controller
{
    private const TEST_OFF = 0, TEST_ALL = 1, TEST_SWARM_LOGIN = 2, TEST_SWARM_GET_USER = 3, TEST_EXTERNAL_ADMIN = 4;

    private int $testMode = self::TEST_OFF;

    /**
     * Display login form.
     */
    public function index()
    {
        $data = [
            'googleRecaptchSiteKey' => SessionEnum::GoogleRecaptchSiteKey->getSession(),
            'reCAPTCHAFieldId' => RequestParamsEnum::GoogleRecaptchaResponse->value,
        ];
        return view('hhh.Site.pages.auth.Betconstruct.login_WebSocket', $data);
    }

    /**
     * Get initial data for javascript api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInitialData(): JsonResponse
    {
        $data = [
            'swarmSrc' => sprintf('%s?version=%s', url('assets/general/js/swarm_api.min.js'), config('hhh_config.ResourceVersion')),
            'targetRedirectUrl' => $this->getTargetRedirectUrl(),
        ];

        return JsonResponseHelper::successResponse($data, 'Success');
    }

    /**
     * Handle login request attempt.
     *
     * @param  \App\Http\Requests\Site\Auth\LoginAttemptRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function attempt(LoginAttemptRequest $request): JsonResponse
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $remember = $request->input('remember');
        $loginData = $request->input('loginData');
        $getUserData = $request->input('getUserData');

        try {

            // Validate swarm responses
            if (!JsonHelper::isJson($loginData) || !JsonHelper::isJson($getUserData))
                return JsonResponseHelper::errorResponse('auth.custom.AccessDenied', __('auth.custom.AccessDenied'), HttpResponseStatusCode::UnprocessableEntity->value);

            $swarmLogin = new LoginRequest($username, $password, null);
            $swarmLogin->setResponse($this->convertWebsocketResponseToApiResponse($loginData));

            $swarmGetUser = new GetUserRequest();
            $swarmGetUser->setResponse($this->convertWebsocketResponseToApiResponse($getUserData));

            $bcUserId = $swarmLogin->getUserId();

            // Fetch client data via external admin API
            if ($this->testMode === self::TEST_ALL || $this->testMode === self::TEST_EXTERNAL_ADMIN)
                $exAdminClients = ExternalAdminApiResponseTest::getClient(); //Do not delete this for quick access when you need to test
            else
                $exAdminClients = ExternalAdminAPI::getClientById($bcUserId);

            if ($exAdminClients->getStatus()->name === ApiStatusEnum::Success->name) {


                $clientData = (new Collection($exAdminClients->getData()))->first();

                $betconstructClient = ClientModelEnum::fillModel($clientData, BetconstructClient::find($bcUserId));
                $betconstructClient[ClientModelEnum::DepositCount->dbName()] = $swarmLogin->getDepositCount();
                $betconstructClient[ClientModelEnum::Password->dbName()] = $password;

                $betconstructSwarmClient = ClientSwarmModelEnum::fillModel($swarmGetUser->getData(), true, BetconstructSwarmClient::find($bcUserId));

                return $this->loginClient($betconstructClient, $betconstructSwarmClient, $remember);
            } else {

                return JsonResponseHelper::errorResponse(null, $exAdminClients->getErrorMessage(), HttpResponseStatusCode::Unauthorized->value);
            }
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Login attempt failed."
            );
        }
        return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'));
    }

    /**
     * Convert websocket response to api response
     *
     * @param  string $wsResponseJson incomming json string response from front-end javascript
     * @return \Illuminate\Http\JsonResponse
     */
    private function convertWebsocketResponseToApiResponse(string $wsResponseJson): JsonResponse
    {
        $response = [
            'code' => 0,
            'data' => json_decode($wsResponseJson, true),
        ];

        return JsonResponseHelper::successResponse($response);
    }

    /**
     * Check whether client is active or not
     *
     * @param  string $username
     * @return bool|string true|error message
     */
    private function isClientActive(string $username): bool|string
    {

        $betconstructClient = BetconstructClient::where(ClientModelEnum::Login->dbName(), $username);

        /**
         * NOTICE:
         *
         * Check the count, the username may have changed on the Betconstruct side
         * and there is more than one user with the same username in the database.
         *
         * If there is more than one user, we will allow the request to be sent and
         * after updating the information in the middleware, we will block the user.
         */
        if ($betconstructClient->count() === 1) {

            /** @var BetconstructClient $betconstructClient */
            // Only need foregn key
            $betconstructClient = $betconstructClient->select(ClientModelEnum::UserId->dbName())->first();

            /** @var UserBetconstruct $userBetconstruct */
            $userBetconstruct = $betconstructClient->userBetconstruct;

            if (is_null($userBetconstruct)) {
                $userBetconstruct = $betconstructClient->userBetconstruct()->withTrashed()->first();

                if (is_null($userBetconstruct)) {
                    // Wrong data in database: "betconstructClient" exists without "userBetconstruct"
                    $betconstructClient->delete();
                    return $this->isClientActive($username);
                } else {
                    // User deleted in admin panel
                    $userBetconstruct->restore();
                }
            }

            if (!$userBetconstruct->isActive()) {

                $accoutStatus = $userBetconstruct[UsersTableEnum::Status->dbName()];

                if ($accoutStatus !== UsersStatusEnum::Active->name)
                    $error = __('auth.custom.AccountStatusMessage', ['status' => UsersStatusEnum::getCase($accoutStatus)->translate()]);
                else
                    $error = __('auth.custom.AccessDenied');

                return $error;
            }
        }

        return true;
    }

    /**
     * Save or update client data and login client
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $betconstructClient
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Models\BetconstructSwarmClient $betconstructSwarmClient
     * @param bool $remember
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginClient(BetconstructClient $betconstructClient, BetconstructSwarmClient $betconstructSwarmClient, bool $remember): JsonResponse
    {
        try {

            if ($betconstructClient[ClientModelEnum::Login->dbName()] != $betconstructSwarmClient[ClientSwarmModelEnum::Username->dbName()]) {
                // The username of client should be same in both models
                return JsonResponseHelper::errorResponse('auth.custom.AccessDenied', __('auth.custom.AccessDenied'), HttpResponseStatusCode::Forbidden->value);
            }

            // Betconstruct Client ID, in users table saved in username
            $userBetconstruct = UserBetconstruct::where(UsersTableEnum::Username->dbName(), $betconstructClient[ClientModelEnum::Id->dbName()])
                ->first();

            $password = $betconstructClient[ClientModelEnum::Password->dbName()];
            $email = sprintf("%s@%s", $betconstructClient[ClientModelEnum::Id->dbName()], ExternalAdminApiConfigEnum::PartnerId->getValue());

            if (is_null($userBetconstruct)) {
                // new user
                $userBetconstruct = new UserBetconstruct();

                $userBetconstruct->forceFill([
                    UsersTableEnum::Username->dbName()  => $betconstructClient[ClientModelEnum::Id->dbName()],
                    UsersTableEnum::Email->dbName()     => $email,
                    UsersTableEnum::Password->dbName()  => Hash::make($password),
                    UsersTableEnum::Type->dbName()      => UsersTypesEnum::BetconstructClient->name,
                    UsersTableEnum::RoleId->dbName()    => $this->getClientCategoryId($betconstructClient, $betconstructSwarmClient),
                    UsersTableEnum::Status->dbName()    => UsersStatusEnum::Active->name,
                ]);
            } else {
                // Existing user

                $userBetconstruct->forceFill([
                    UsersTableEnum::Password->dbName()  => Hash::make($password),
                    UsersTableEnum::Email->dbName()     => $email,
                    UsersTableEnum::RoleId->dbName()    => $this->getClientCategoryId($betconstructClient, $betconstructSwarmClient),
                ]);
            }

            if ($userBetconstruct->save()) {

                $userId = $userBetconstruct[UsersTableEnum::Id->dbName()];

                $betconstructClient[ClientModelEnum::UserId->dbName()] = $userId;
                // Save the password so that it can be retrieved so that it can be used in API calls
                $betconstructClient[ClientModelEnum::Password->dbName()] = Crypt::encrypt($password);
                // This record must be updated so that if the user information is not changed, the client will not be logged out by the "NeedToLoginAgain" middleware.
                $betconstructClient[TimestampsEnum::UpdatedAt->dbName()] = now();

                $betconstructClient->save();

                $betconstructSwarmClient[ClientSwarmModelEnum::UserId->dbName()] = $userId;
                $betconstructSwarmClient->save();


                // Check client status before login
                $isClientActive = $this->isClientActive($betconstructClient[ClientModelEnum::Login->dbName()]);
                if ($isClientActive !== true) {
                    return JsonResponseHelper::errorResponse(null, $isClientActive, HttpResponseStatusCode::Forbidden->value);
                }

                Auth::login($userBetconstruct, $remember);
                return JsonResponseHelper::successResponse(null);
            }
        } catch (\Throwable $th) {
            //throw $th;

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                "Failed to save Betconstruct client."
            );

            return JsonResponseHelper::errorResponse('bc_api.UnknownError', __('bc_api.UnknownError'));
        }
    }

    /**
     * Get client category
     *
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Models\BetconstructClient $betconstructClient
     * @param  \App\HHH_Library\ThisApp\API\Betconstruct\SwarmApi\Models\BetconstructSwarmClient $betconstructSwarmClient
     * @return int
     */
    public function getClientCategoryId(BetconstructClient $betconstructClient, BetconstructSwarmClient $betconstructSwarmClient): int
    {

        // Default client category
        $clientDefaultCategory = ClientCategory::where(RolesTableEnum::Name->dbName(), ClientCategoryReservedEnum::NormalUser->value)
            ->first();

        if (is_null($clientDefaultCategory)) {

            throw new Exception(sprintf(
                'The default client category (%s) does not exist in the database.',
                ClientCategoryReservedEnum::NormalUser->value
            ));
        }

        $customPlayerCategory = $betconstructClient[ClientModelEnum::CustomPlayerCategory->dbName()] . "";
        $loyaltyLevelId = $betconstructSwarmClient[ClientSwarmModelEnum::LoyaltyLevelId->dbName()] . "";

        $mapTypeCol = ClientCategoryMapsTableEnum::MapType->dbName();
        $mapValueCol = ClientCategoryMapsTableEnum::ItemValue->dbName();
        $mapPriorityCol = ClientCategoryMapsTableEnum::Priority->dbName();

        $matchedMap = ClientCategoryMap::Active()
            ->where(function (Builder $query) use ($mapTypeCol, $mapValueCol, $customPlayerCategory, $loyaltyLevelId) {

                $query
                    ->where(function (Builder $query) use ($mapTypeCol, $mapValueCol, $customPlayerCategory) {

                        $query->where($mapTypeCol, ClientCategoryMapTypesEnum::CustomCategory->name)
                            ->where($mapValueCol, $customPlayerCategory);
                    })
                    ->orWhere(function (Builder $query) use ($mapTypeCol, $mapValueCol, $loyaltyLevelId) {

                        $query->where($mapTypeCol, ClientCategoryMapTypesEnum::LoyaltyLevel->name)
                            ->where($mapValueCol, $loyaltyLevelId);
                    });
            })
            ->orderBy($mapPriorityCol, 'asc')
            ->first();

        return is_null($matchedMap) ? $clientDefaultCategory->id : $matchedMap[ClientCategoryMapsTableEnum::RoleId->dbName()];
    }

    /**
     * Get target redirect URL after login
     *
     * @return string
     */
    private function getTargetRedirectUrl(): string
    {
        $lastVisitedPageUrl = GeneralSessionsEnum::LastVisitedPageUrl->getSession(SitePublicRoutesEnum::MainPage->url());

        return Str::of($lastVisitedPageUrl)->contains(config('app.domain')) ? $lastVisitedPageUrl : SitePublicRoutesEnum::MainPage->url();
    }
}
