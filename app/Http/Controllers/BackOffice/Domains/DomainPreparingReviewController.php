<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\HHH_Library\general\php\Enums\CastEnum;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Models\BackOffice\Domains\Domain;
use App\Models\BackOffice\Domains\DomainPreparingReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DomainPreparingReviewController extends SuperController
{

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainPreparingReview::class);

        $data = [];

        return view('hhh.BackOffice.pages.Domains.DomainPreparingReview.index', $data);
    }

    /**
     * Get initial data
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDomainForReview(Request $request): JsonResponse
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainPreparingReview::class);

        try {
            $idCol = TableEnum::Id->dbName();
            $nameCol = TableEnum::Name->dbName();
            $descrCol = TableEnum::Descr->dbName();

            $lastPassedId = $request->input('lastPassedId');

            $domainQuery = DomainPreparingReview::select($idCol, $nameCol, $descrCol)
                ->where($idCol, '>', $lastPassedId)
                ->orderBy($idCol, 'asc');

            $domain = null;
            if ($request->has('forceId'))
                $domain = Domain::find($request->input('forceId'));

            if (is_null($domain))
                $domain = $domainQuery->first();

            $data = [
                'domain' => $domain,
                'remainingDomainsCount' => number_format($domainQuery->count()),
            ];

            return JsonResponseHelper::successResponse($this->makeResponseData($data), 'Success');
        } catch (\Throwable $th) {
            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }
    }
    // yixghgapojcnz
    /**
     * Submit review result
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitReview(Request $request): JsonResponse
    {

        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainPreparingReview::class);

        try {
            $mobileLoadRealut = CastEnum::Boolean->cast($request->input('mobileLoadRealut'));
            $desktopLoadRealut = CastEnum::Boolean->cast($request->input('desktopLoadRealut'));
            $domainName = $request->input('domain');
            $reviewerDescr = $request->input('descr');

            $domain = Domain::where(TableEnum::Name->dbName(), $domainName)->first();

            if (!is_null($domain)) {

                $domain[TableEnum::Descr->dbName()] = trim($reviewerDescr);

                if ($mobileLoadRealut && $desktopLoadRealut)
                    $domain[TableEnum::Status->dbName()] = DomainStatusEnum::ReadyToUse->name;
                else
                    $domain[TableEnum::Status->dbName()] = DomainStatusEnum::Preparing->name;

                $domain->save();

                return JsonResponseHelper::successResponse($this->makeResponseData(null), 'Success');
            } else
                return JsonResponseHelper::errorResponse(null, __('PagesContent_DomainPreparingReview.messages.errorDomainNotFound', ['domain', $domainName]), HttpResponseStatusCode::NotFound->value);
        } catch (\Throwable $th) {

            return JsonResponseHelper::errorResponse(null, $th->getMessage(), HttpResponseStatusCode::BadRequest->value);
        }
    }

    /**
     * Attach default data and make final response data
     *
     * @param  ?array $data
     * @return array
     */
    protected function makeResponseData(?array $data): array
    {
        $defaultData = [
            'csrfToken'         => csrf_token(),
            'debugMode'         => (bool) config('app.debug'),
        ];

        return (is_null($data)) ? $defaultData : array_merge($defaultData, $data);
    }
}
