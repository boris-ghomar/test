<?php

namespace App\Http\Controllers\BackOffice;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DomainsTableEnum;
use App\Enums\Domains\DomainStatusEnum;
use App\HHH_Library\ThisApp\Packages\Client\Domain\DomainAssignmentEngine;
use App\Http\Controllers\Controller;
use App\Models\BackOffice\Domains\Domain;
use App\Models\User;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    /**
     * Site dashboard index
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $data = [
            'domainSection' => $this->getDomainSectionData(),
        ];

        return view('hhh.BackOffice.pages.Dashboard.index', $data);
    }

    /**
     * Get domain section data
     *
     * @return ?array
     */
    private function getDomainSectionData(): ?array
    {
        $user = User::authUser();

        if (!$user->can(PermissionAbilityEnum::viewAny->name, Domain::class))
            return null;

        $assignableDomainsStatistics = [];

        $domainAssigningCategoryIds = DomainAssignmentEngine::getDomainAssigningCategoryIds();
        $domainStatusArray = DomainStatusEnum::cases();

        /** @var  DomainStatusEnum $domainStatus*/
        foreach ($domainStatusArray as $domainStatus) {

            $count = Domain::whereIn(DomainsTableEnum::DomainCategoryId->dbName(), $domainAssigningCategoryIds)
                ->where(DomainsTableEnum::Status->dbName(), $domainStatus->name)
                ->count();

            $assignableDomainsStatistics[$domainStatus->translate()] = number_format($count);
        }

        $data = [
            'assignableDomainsStatistics' => $assignableDomainsStatistics,
        ];

        return $data;
    }
}
