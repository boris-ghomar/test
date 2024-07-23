<?php

namespace App\Http\Controllers\Site;

use App\Enums\Database\Tables\AssignedDomainsTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\Enums\Routes\SitePublicRoutesEnum;
use App\Enums\Settings\AppTechnicalSettingsEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\HHH_Library\general\php\LogCreator;
use App\HHH_Library\ThisApp\API\Betconstruct\ExternalAdmin\Enums\ClientModelEnum;
use App\HHH_Library\ThisApp\Packages\Client\Domain\DomainAssignmentEngine;
use App\HHH_Library\ThisApp\Packages\Client\TrustScore\ClientTrustScoreEngine;
use App\Http\Controllers\Controller;
use App\Models\BackOffice\Domains\AssignedDomain;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{

    private User $user;

    /**
     * Site dashboard index
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if (!auth()->check())
            return redirect(SitePublicRoutesEnum::defaultLogin()->url());

        if ($user = User::authUser()) {
            if ($user->isPersonnel())
                return redirect(AdminPublicRoutesEnum::Dashboard->url());
        }

        $this->user = $user;

        $data = [
            'domainSection'         => $this->getDomainSectionData(),
            'telegramBotSection'    => $this->getTelegramBotSectionData(),
        ];
        return view('hhh.Site.pages.Dashboard.index', $data);
    }

    /**
     * Get domain section data
     *
     * @return array
     */
    private function getDomainSectionData(): array
    {
        $domainAssignmentEngine = new DomainAssignmentEngine();

        $domain = $domainAssignmentEngine->getDomain();

        if (is_null($domain)) {
            $domainId = null;
            $showReportBtn = false;
            $isDomainReported = false;
        } else {
            $domainId = $domain->id;
            $userId = auth()->user()->id;

            // Check if domain was reported by client
            $assignedDomain = AssignedDomain::where(AssignedDomainsTableEnum::DomainId->dbName(), $domainId)
                ->where(AssignedDomainsTableEnum::UserId->dbName(), $userId)
                ->where(AssignedDomainsTableEnum::Reported->dbName(), 1)
                ->first();

            $isDomainReported = is_null($assignedDomain) ? false : true;
            $showReportBtn = !$isDomainReported;
        }

        return [
            'bcUnblockedDomain' => $domainAssignmentEngine->getDomainName(),
            'bcPermenantDomain' => AppTechnicalSettingsEnum::DoAsSy_PermanentDomain->getValue(),
            'domainId'          => is_null($domainId) ? null : Crypt::encrypt($domainId),
            'showReportBtn'     => $showReportBtn,
            'isDomainReported'  => $isDomainReported,
        ];
    }

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
     * Report domain issue by client
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiReportDomainIssue(Request $request): JsonResponse
    {
        $domainIdKey = 'domainId';

        $accessDenied = JsonResponseHelper::errorResponse('thisApp.Errors.accessDenied', __('thisApp.Errors.accessDenied'), HttpResponseStatusCode::Forbidden->value);

        if (!$request->has($domainIdKey))
            return $accessDenied;

        try {
            $domainId = Crypt::decrypt($request->input($domainIdKey));
        } catch (\Throwable $th) {
            return $accessDenied;
        }

        try {
            $userId = auth()->user()->id;

            $assignedDomain = AssignedDomain::where(AssignedDomainsTableEnum::DomainId->dbName(), $domainId)
                ->where(AssignedDomainsTableEnum::UserId->dbName(), $userId)
                ->first();

            if (is_null($assignedDomain))
                return $accessDenied;

            $assignedDomain[AssignedDomainsTableEnum::Reported->dbName()] = true;
            $assignedDomain[AssignedDomainsTableEnum::ReportedAt->dbName()] = Carbon::now();
            $assignedDomain->save();

            /**
             * If the user does not have a trust score,
             * the assigned domain is fake and blocked,
             * so there is no need to flag it for verification.
             */
            if ($assignedDomain[AssignedDomainsTableEnum::ClientTrustScore->dbName()] > 0) {

                $domain = $assignedDomain->domain;
                if ($domain[DomainsTableEnum::Status->dbName()] != DomainStatusEnum::Blocked->name) {

                    $domain[DomainsTableEnum::Reported->dbName()] = true;
                    $domain->save();

                    ClientTrustScoreEngine::assignedDomainReported($assignedDomain);
                }
            }

            $data = $this->getDomainSectionData();
            $isNewDomainAssigned = $data['isDomainReported'] ? false : true;

            $message = $isNewDomainAssigned ? __('thisApp.Site.Dashboard.msg.DomainReplaced') : __('thisApp.Site.Dashboard.msg.DomainReported');
            return JsonResponseHelper::successResponse($this->makeApiResponseData($data), $message);
        } catch (\Throwable $th) {

            LogCreator::createLogError(
                __CLASS__,
                __FUNCTION__,
                $th->getMessage(),
                'Dashboard domain report'
            );

            return JsonResponseHelper::errorResponse('error.UnknownError', __('error.UnknownError'), HttpResponseStatusCode::InternalServerError->value);
        }
    }

    /**
     * Get telegram bot section data
     *
     * @return array
     */
    private function getTelegramBotSectionData(): array
    {
        $clientExtra = $this->user->betconstructClient;

        if (is_null($clientExtra))
            Auth::logout();

        $telegramBotJoinLink = sprintf("https://telegram.me/betcartbot?start=%s", $clientExtra[ClientModelEnum::Id->dbName()]);

        return [
            'joinLink'   => $telegramBotJoinLink,
        ];
    }
}
