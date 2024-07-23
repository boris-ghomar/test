<?php

namespace App\HHH_Library\Export;

use App\Enums\Resources\FileConfigEnum;
use App\HHH_Library\general\php\FileAssistant;

class DownloadExportedFile
{

    /**
     * Download generated excel file.
     *
     * The saved name should be separate from the download name,
     * as multiple users may be exporting a file from a section
     * with different filters at the same time, which prevents
     * data from being overwritten.
     *
     * @param  string $savedFileName : The file with this name is stored on the server.
     * @param  string $downloadFileName : The file with this name will be downloaded for the user..
     * @return void
     */
    public function downloadExcelFile($savedFileName, $downloadFileName)
    {

        $savedFileName = base64_decode($savedFileName);
        $downloadFileName = base64_decode($downloadFileName);

        $fileAssistant = new FileAssistant(FileConfigEnum::ExportExcel, $savedFileName);
        $pathToFile = $fileAssistant->getRelativePath();

        /**
         * Delete files that were not generated today.
         * Because a file may have been generated in the
         * previous days and remained here because it was not downloaded.
         */
        $fileAssistant->deleteFile($fileAssistant->files(date('Y-m-d'), false));

        $size = filesize($pathToFile);

        $headers = [
            'Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition: attachment; filename="' . $downloadFileName . '"',
            'Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', time()),
            'Content-Length: ' . $size,
        ];

        return response()
            ->download($pathToFile, $downloadFileName, $headers)
            ->deleteFileAfterSend(true);
    }
}
