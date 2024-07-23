<?php

namespace App\Http\Controllers\BackOffice\Domains;

use App\Enums\AccessControl\PermissionAbilityEnum;
use App\Enums\Database\Tables\DomainExtensionsTableEnum;
use App\Enums\Database\Tables\DomainsTableEnum as TableEnum;
use App\Enums\Settings\AppSettingsEnum;
use App\HHH_Library\general\php\DropdownListCreater;
use App\Http\Controllers\SuperClasses\SuperController;
use App\Http\Requests\BackOffice\Domains\DomainGeneratorRequest;
use App\Models\BackOffice\Domains\Domain;
use App\Models\BackOffice\Domains\DomainExtension;
use App\Models\BackOffice\Domains\DomainGenerator;
use Illuminate\Support\Str;

class DomainGeneratorController extends SuperController
{

    const MAX_NAME_GENERATE_TRY = 10;

    private int $nameGenerateTryCount = 0;

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $this->authorize(PermissionAbilityEnum::viewAny->name, DomainGenerator::class);

        $domainExtensionQuery = DomainExtension::where(DomainExtensionsTableEnum::IsActive->dbName(), 1)
            ->where(DomainExtensionsTableEnum::LimitedOrder->dbName(), 0);
        $domainExtensionCollection = DropdownListCreater::makeByModelQuery($domainExtensionQuery, DomainExtensionsTableEnum::Name->dbName())
            ->sort()->get();

        $data = [
            'domainExtensionCollection' => $domainExtensionCollection,
        ];

        return view('hhh.BackOffice.pages.Domains.DomainGenerator.index', $data);
    }

    /**
     * Generate domain list
     *
     * @param  \App\Http\Requests\BackOffice\Domains\DomainGeneratorRequest $request
     * @return mixed
     */
    public function generate(DomainGeneratorRequest $request)
    {

        $domainCount = $request->input('DomainCount');
        $domainLettersCount = $request->input('DomainLettersCount');
        $excludeLetters = strtolower($request->input('ExcludeLetters'));
        $domainExtensionId = $request->input('DomainExtension');

        // Save last changes to settings
        AppSettingsEnum::DomainGeneratorDomainCount->setValue($domainCount);
        AppSettingsEnum::DomainGeneratorDomainLettersCount->setValue($domainLettersCount);
        AppSettingsEnum::DomainGeneratorExcludeLetters->setValue($excludeLetters);
        AppSettingsEnum::DomainGeneratorDomainExtension->setValue($domainExtensionId);

        if (empty($excludeLetters)) {
            $excludeLetters = [];
        } else
            $excludeLetters = explode("+", $excludeLetters);

        $domainExtension = DomainExtension::find($domainExtensionId);
        $domainExtensionName = $domainExtension[DomainExtensionsTableEnum::Name->dbName()];


        $domains = [];
        for ($i = 0; $i < $domainCount; $i++) {

            // Reset try counter
            $this->nameGenerateTryCount = 0;

            $domain = $this->generateDomainName($domains, $domainLettersCount, $domainExtensionName, $excludeLetters);
            array_push($domains, $domain);
        }

        $data = [
            'domains' => json_encode($domains),
        ];
        return redirect()->back()->withInput()->with($data);
    }

    /**
     * Generate domain name
     *
     * @param  array $generatedDomains
     * @param  int $domainLettersCount
     * @param  string $domainExtensionName
     * @param  array $excludeLetters
     * @return string
     */
    private function generateDomainName(array $generatedDomains, int $domainLettersCount, string $domainExtensionName, array $excludeLetters): string
    {

        $domainName = Str::random($domainLettersCount);
        $domainName = str_replace(
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
            ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'],
            $domainName
        );
        $domain = sprintf("%s.%s", $domainName, $domainExtensionName);
        $domain = strtolower($domain);

        // Check previous generated domains
        if (in_array($domain, $generatedDomains)) {

            $this->nameGenerateTryCount++;
            if ($this->nameGenerateTryCount < self::MAX_NAME_GENERATE_TRY)
                return $this->generateDomainName($generatedDomains, $domainLettersCount, $domainExtensionName, $excludeLetters);
            else
                return __('PagesContent_DomainGenerator.messages.ErrorGeneratingNewDomain');
        }

        // Check exclude letters
        foreach ($excludeLetters as $excludeLetter) {

            if (Str::of($domain)->contains($excludeLetter)) {

                $this->nameGenerateTryCount++;
                if ($this->nameGenerateTryCount < self::MAX_NAME_GENERATE_TRY)
                    return $this->generateDomainName($generatedDomains, $domainLettersCount, $domainExtensionName, $excludeLetters);
                else
                    return __('PagesContent_DomainGenerator.messages.ErrorGeneratingNewDomain');
            }
        }

        // Check previous domains
        if (Domain::where(TableEnum::Name->dbName(), 'like', "%" . $domain . "%")->withTrashed()->exists()) {

            $this->nameGenerateTryCount++;
            if ($this->nameGenerateTryCount < self::MAX_NAME_GENERATE_TRY)
                return $this->generateDomainName($generatedDomains, $domainLettersCount, $domainExtensionName, $excludeLetters);
            else
                return __('PagesContent_DomainGenerator.messages.ErrorGeneratingNewDomain');
        }

        return $domain;
    }
}
