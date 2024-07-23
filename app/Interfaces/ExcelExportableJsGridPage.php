<?php

namespace App\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ExcelExportableJsGridPage
{

    /**
     * This function exports the table information
     * to an Excel file and downloads it to the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportExcel(Request $request): JsonResponse;

    /* Sample */
    /*
    public function exportExcel(Request $request): JsonResponse
    {
        $this->authorize(PermissionAbilityEnum::export->name, AssignedDomain::class);

        $exporter = new AssignedDomainExport($request->all());
        return $exporter->export();
    }
    */
}
