<?php

namespace App\Enums\Resources\Interfaces;

interface FileConfigInterface
{

    /**
     * Get storage disk
     *
     * @return string
     */
    public function disk(): string;

    /**
     * Get storage disk
     *
     * @return string
     */
    public function path(): string;

    /**
     * Get acceptable mimes of image.
     *
     * @param  mixed $getAsArray (optinal) return $getAsArray ? array : string (sample: 'pdf,txt');
     * @return string|array
     */
    public function mimes(bool $getAsArray = false): string|array;

    /**
     * Get acceptable mimes of image for use in upload input field.
     *
     * @param  mixed $getAsArray (optinal) return $getAsArray ? array : string (sample: '.pdf,.txt');
     * @return string|array
     */
    public function acceptableMimesForUpload(bool $getAsArray = false): string|array;
}
