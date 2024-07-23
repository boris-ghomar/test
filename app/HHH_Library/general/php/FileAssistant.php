<?php

namespace App\HHH_Library\general\php;

use App\Enums\Resources\FileConfigEnum;
use App\Enums\Resources\ImageConfigEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileAssistant
{

    /**
     * Everyhing about File system:
     * \vendor\laravel\framework\src\Illuminate\Contracts\Filesystem\Filesystem.php
     */

    protected $fileConfig = null;
    protected $disk = 'root_public'; //default; comes from: config/filesystems.php
    protected $path;
    protected $name;

    /**
     * Class constructor.
     *
     * @param FileConfigEnum|ImageConfigEnum|null $fileConfig
     * @param  ?string $name : Saved "file name" in folder
     */
    public function __construct(FileConfigEnum|ImageConfigEnum|null $fileConfig, ?string $name = null)
    {
        $this->setFileConfig($fileConfig);
        $this->setName($name);
    }

    /****************** Setters **********************/

    /**
     * Set disk name
     *
     * @param FileConfigEnum|ImageConfigEnum|null $fileConfig
     * @return void
     */
    public function setFileConfig(FileConfigEnum|ImageConfigEnum|null $fileConfig): void
    {
        $this->fileConfig = $fileConfig;

        if (!is_null($fileConfig)) {

            $this->setDisk($fileConfig->disk());
            $this->setPath($fileConfig->path());
        }
    }

    /**
     * Set disk name
     *
     * @param  ?string $disk
     * @return void
     */
    public function setDisk(?string $disk): void
    {
        $disk = Str::of($disk)->trim()->toString();

        if (!Str::of($disk)->isEmpty()) {

            $this->disk = $disk;
        }
    }

    /**
     * Set file path
     *
     * @param  ?string $path
     * @return void
     */
    public function setPath(?string $path): void
    {
        $path = Str::of($path)->trim();

        if (!Str::of($path)->isEmpty()) {

            if (!Str::endsWith($path, "/"))
                $path .= "/";
        }

        $this->path = is_string($path) ? $path : $path->toString();
    }

    /**
     * Set file name
     *
     * @param  ?string $name
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = is_null($name) ? null : Str::of($name)->trim()->toString();
    }
    /****************** Setters END **********************/

    /****************** Getters **********************/

    /**
     * Get FileConfig
     *
     * @return FileConfigEnum|ImageConfigEnum|null
     */
    public function getFileConfig(): FileConfigEnum|ImageConfigEnum|null
    {
        return $this->fileConfig;
    }

    /**
     * Get disk
     *
     * @return ?string
     */
    public function getDisk(): ?string
    {
        return $this->disk;
    }

    /**
     * Get file path
     *
     * @return ?string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Get file name
     *
     * @return ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * This function merges the path and name of the file
     * and returns the full name of the file.
     *
     * @return ?string
     */
    public function getFullName(): ?string
    {
        return $this->getPath() . $this->getName();
    }

    /**
     * Get relative path of the file.
     *
     * @return ?string
     */
    public function getRelativePath(): ?string
    {
        /** @var Storage $disk */
        $disk = Storage::disk($this->getDisk());

        return $disk->path($this->getFullName());
    }

    /**
     * This function returns the actual access path to the file.
     *
     * @return ?string
     */
    public function getUrl(): ?string
    {
        return url($this->getFullName());
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param  string $filter : The string that wants to be filtered
     * @param bool $contains :
     *                  true ? Returns strings that contain filtered strings ($filter).
     *                  false ? Returns strings that NOT contain filtered strings ($filter).
     * @return array
     */
    public function files(string $filter = null, bool $contains = true): array
    {
        $files = Storage::disk($this->getDisk())
            ->files($this->getPath());

        if (!is_null($filter)) {

            return array_filter($files, function ($item) use ($filter, $contains) {
                return $contains ? strpos($item, $filter) : !strpos($item, $filter);
            });
        }

        return $files;
    }

    /****************** Getters END **********************/

    /**
     * This function checks the accuracy of the input items.
     *
     * @return bool
     */
    private function checkInputItems(): bool
    {
        if (Str::of($this->path)->isEmpty()) return false;
        if (Str::of($this->name)->isEmpty()) return false;

        return true;
    }

    /**
     * This function checks if there is a file
     * with these properties or not.
     *
     * @return bool
     */
    public function isFileExists(): bool
    {
        try {
            if ($this->checkInputItems()) {

                return Storage::disk($this->getDisk())->exists($this->getFullName());
            }
        } catch (\Throwable $th) {
        }

        return false;
    }


    /**
     * Store uploaded file from form
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $fileInputFieldName The input field name in upload HTML form
     * @param bool $deleteLastFile
     * @return ?string storedFileName Or null if file not uploaded
     */
    public function storeUploadedFile(Request  $request, string $fileInputFieldName, bool $deleteLastFile = true): ?string
    {
        $storedFileName = null;

        if ($request->hasFile($fileInputFieldName)) {
            if ($request->file($fileInputFieldName)->isValid()) {

                $path = $request->file($fileInputFieldName)->store($this->getPath(), ['disk' => $this->getDisk()]);
                $storedFileName = basename($path);
            }
        }

        if ($deleteLastFile && !is_null($storedFileName)) {
            /**
             * The new file has been uploaded,
             * so the previous file will be deleted from the disk.
             */
            $this->deleteFile();
        }

        return $storedFileName;
    }

    /**
     * Delete file/files from storage.
     * You can also send an array of files to be removed from disk.
     *
     * Example:
     *  $fileAssistant->deleteFile($fileAssistant->files('test'));
     *
     * @param  string|array $path -optional- : If this value is null, the introduced file will be deleted.
     * @return void
     */
    public function deleteFile(string|array $path = null): void
    {
        if (is_null($path)) {

            if ($this->isFileExists()) {
                Storage::disk($this->disk)->delete($this->getFullName());
            }
        } else {
            Storage::disk($this->disk)->delete($path);
        }
    }

    /**
     * Create empty file
     *
     * @param  bool $overwrite
     * @return bool
     */
    public function createEmptyFile(bool $overwrite = false): bool
    {
        if ($this->checkInputItems()) {

            if (!$this->isFileExists() || $overwrite )
                return Storage::disk($this->disk)->put($this->getFullName(), '');
        }

        return false;
    }
}
