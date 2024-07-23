<?php

namespace App\HHH_Library\Export\SuperClasses;

use App\Enums\Resources\FileConfigEnum;
use App\Enums\Routes\AdminPublicRoutesEnum;
use App\HHH_Library\Export\Excel\SimpleXLSXGen;
use App\HHH_Library\general\php\Enums\HttpResponseStatusCode;
use App\HHH_Library\general\php\FileAssistant;
use App\HHH_Library\general\php\JsonResponseHelper;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

abstract class SuperExport
{
    const MAX_ALLOWED_EXPORT_RECORDS_COUNT = 3000;

    protected $filterData = [];
    protected $dataModel = null;

    /**
     * Construction function
     *
     * @param ?array $filterData :: Data array related to filtering DB results
     */
    public function __construct(?array $filterData)
    {
        $this->setFilterData($filterData);
        $this->setDataModelRelation($this->getFilterData());
    }
    /******************** Implements ********************/

    /**
     * Specify the name of the data sheet in this function.
     *
     * @return string
     */
    public abstract function sheetName(): string;

    /**
     * This function returns the data model collection.
     *
     * @param array $filterData
     * @return \Illuminate\Database\Eloquent\Builder Exp:: return DomainHolderAccount::ApiIndexCollectionta);
     */
    public abstract function dataModelRelation(array $filterData): Builder;

    /**
     * This function returns column titles .
     *
     * @return array
     */
    public abstract function titleRow(): array;

    /**
     * This function returns a collection of requested data.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public abstract function dataCollection(): ResourceCollection;

    /******************** Implements ********************/

    /**
     * Max allowed records count per export
     *
     * @return int
     */
    protected function maxAllowedRecordsCounts(): int
    {
        return self::MAX_ALLOWED_EXPORT_RECORDS_COUNT;
    }

    /**
     * This function creates the relevant export file and returns the download link.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(): JsonResponse
    {
        $validate = $this->validate();

        if ($validate === true) {

            return $this->generateExcelFile();
        } else
            return $validate;
    }

    /**
     * This function generates the requested Excel file
     * and returns its download link.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function generateExcelFile(): JsonResponse
    {
        $fileExtension = "xlsx";

        /**
         * The saved name should be separate from the download name,
         * as multiple users may be exporting a file from a section
         * with different filters at the same time, which prevents
         * data from being overwritten.
         */
        $savedFileName = sprintf(
            '%s_%s.%s',
            Str::random(16),
            date('Y-m-d_H-i-s'),
            $fileExtension
        );

        $fileAssistant = new FileAssistant(FileConfigEnum::ExportExcel, $savedFileName);
        $fileAssistant->createEmptyFile();

        $simpleXLSXGen = new SimpleXLSXGen;
        $simpleXLSXGen->addSheet($this->collectData(), $this->sheetName());
        $simpleXLSXGen->saveAs($fileAssistant->getRelativePath());

        $responceData = [
            'downloadLink' => AdminPublicRoutesEnum::ExportExcel->route([base64_encode($savedFileName), base64_encode($this->downloadFileName($fileExtension))]),
        ];
        return JsonResponseHelper::successResponse($responceData, trans('general.export.success.download'), HttpResponseStatusCode::OK->value);
    }

    /**
     * This function provides the information needed for export.
     *
     * @return array
     */
    private function collectData(): array
    {
        $data = array_merge(
            array($this->titleRowStyle()),
            json_decode($this->dataCollection()->toJson(), true)
        );

        return $data;
    }

    /**
     * Generate sheet title row style
     *
     * @return array
     */
    private function titleRowStyle(): array
    {
        $titleRow = [];

        foreach ($this->titleRow() as $title) {

            array_push($titleRow, sprintf('<middle><center><b>%s</b></center></middle>', $title));
        }

        return $titleRow;
    }

    /**
     * Validation of export request
     *
     * @return true|\Illuminate\Http\JsonResponse
     */
    private function validate(): bool|JsonResponse
    {

        $dataRecordsCount = $this->getDataModelRelation()->count();

        $validate = $this->minRecordsCountValidation($dataRecordsCount);
        if ($validate !== true)
            return $validate;

        $validate = $this->maxRecordsCountValidation($dataRecordsCount);
        if ($validate !== true)
            return $validate;

        return true;
    }

    /**
     * Min Records Count Validation
     *
     * @param int $dataRecordsCount
     * @return true|\Illuminate\Http\JsonResponse
     */
    private function minRecordsCountValidation(int $dataRecordsCount): bool|JsonResponse
    {

        if ($dataRecordsCount < 1) {

            return JsonResponseHelper::errorResponse(
                'validation.custom.export.noRecords',
                trans('validation.custom.export.noRecords'),
                HttpResponseStatusCode::BadRequest->value
            );
        }

        return true;
    }

    /**
     * Max Records Count Validation
     *
     * @param int $dataRecordsCount
     * @return true|\Illuminate\Http\JsonResponse
     */
    private function maxRecordsCountValidation(int $dataRecordsCount): bool|JsonResponse
    {
        // Ignore Max allowed records for superAdmin

        /** @var User $user */
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return true;
        }

        $maxAllowedRecordsCounts = $this->maxAllowedRecordsCounts();

        if ($dataRecordsCount > $maxAllowedRecordsCounts) {

            return JsonResponseHelper::errorResponse(
                'validation.custom.export.maxAllowedExportRecordsCount',
                trans('validation.custom.export.maxAllowedExportRecordsCount', [
                    'max' => number_format($maxAllowedRecordsCounts),
                    'num' => number_format($dataRecordsCount)
                ]),
                HttpResponseStatusCode::BadRequest->value
            );
        }

        return true;
    }

    /**************** Setter & Getter *****************/

    /**
     * Set filter data
     *
     * @param ?array $filterData :: Data array related to filtering DB results
     * @return void
     */
    public function setFilterData(?array $filterData): void
    {
        $this->filterData = is_null($filterData) ? [] : $filterData;
    }

    /**
     * Get filter data
     *
     * @return array $filterData :: Data array related to filtering DB results
     */
    public function getFilterData(): array
    {
        return is_null($this->filterData) ? [] : $this->filterData;
    }

    /**
     * Set Data Model Relation
     *
     * @param array $filterData :: Data array related to filtering DB results
     * @return void
     */
    public function setDataModelRelation(array $filterData): void
    {
        $this->dataModel = $this->dataModelRelation($filterData);
    }

    /**
     * Get filter data
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getDataModelRelation(): ?Builder
    {
        return $this->dataModel;
    }
    /**************** Setter & Getter END *****************/

    /**************** Extra Public functions *****************/

    /**
     * This function generates a string
     * based on the date and time of the user
     * to name the file.
     *
     * @return string
     */
    public function generateUserDateTimeSaveString(): string
    {
        /** @var User $user */
        $user = auth()->user();

        return str_replace(
            [' ', ':', '/'],
            ['_', '-', '-'],
            $user->convertUTCToLocalTime(date('Y-m-d H:i:s'))
        );
    }

    /**
     * Specify the file name that the user downloads
     * in this function.
     *
     * You can ovverride this if you need.
     *
     * @return string
     */
    public function downloadFileName(string $fileExtension): string
    {

        return sprintf(
            '%s_%s.%s',
            Str::studly($this->sheetName()),
            $this->generateUserDateTimeSaveString(),
            $fileExtension
        );
    }
    /**************** Extra Public functions END *****************/
}
