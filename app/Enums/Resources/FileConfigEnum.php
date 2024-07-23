<?php

namespace App\Enums\Resources;

use App\Enums\Resources\Interfaces\FileConfigInterface;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum FileConfigEnum implements FileConfigInterface
{
    use EnumActions;

    case ExportExcel;

    /**
     * Get storage disk
     *
     * @return string
     */
    public function disk(): string
    {
        return match ($this) {
            default => 'root_public' // disk name comes from: config/filesystems.php
        };
    }

    /**
     * Get storage disk
     *
     * @return string
     */
    public function path(): string
    {
        return match ($this) {

            self::ExportExcel   => 'assets/download/exports/excel/',

            default             => 'root_public'
        };
    }

    /**
     * Get acceptable mimes of image.
     *
     * @param  mixed $getAsArray (optinal) return $getAsArray ? array : string (sample: 'pdf,txt');
     * @return string|array
     */
    public function mimes(bool $getAsArray = false): string|array
    {
        $res =  match ($this) {

            self::ExportExcel   => ['xlsx'],

            default => []
        };

        return $getAsArray ? $res : implode(",", $res);
    }

    /**
     * Get acceptable mimes of image for use in upload input field.
     *
     * @param  mixed $getAsArray (optinal) return $getAsArray ? array : string (sample: '.pdf,.txt');
     * @return string|array
     */
    public function acceptableMimesForUpload(bool $getAsArray = false): string|array
    {
        $res = [];
        $mimes = $this->mimes(true);

        foreach ($mimes as $mime) {
            array_push($res, '.' . $mime);
        }

        return $getAsArray ? $res : implode(",", $res);
    }
}
