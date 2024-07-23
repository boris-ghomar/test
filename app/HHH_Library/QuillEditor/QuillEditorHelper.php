<?php

namespace App\HHH_Library\QuillEditor;

use App\HHH_Library\general\php\Enums\PregPatternEnum;
use App\HHH_Library\general\php\JsonHelper;
use App\HHH_Library\general\php\TextAnalyzer;
use Illuminate\Support\Facades\Storage;


class QuillEditorHelper
{

    /**
     * The instance itself
     * @var self $instance
     */
    private static $instance;

    private $storagePath = "posts_content/"; // : /storage/post_content/

    const DATA_FILE_EXTENSION = "_data.quill";
    const HTML_FILE_EXTENSION = "_html.quill";

    private $originData = null;
    private $htmlData = null;
    private array $dataList = [];

    private TextAnalyzer $textAnalyzer;

    /**
     * Returns the instance of the class.
     *
     * @return self
     */
    private static function getInstance(): self
    {
        // Create it if it doesn't exist.
        return !self::$instance ? new self() : self::$instance;
    }

    /**
     * Set content via input text
     *
     * @param  ?string $inputData json string data of editor
     * @param  ?string $inputHtml editor html data
     * @return self
     */
    public static function setContent(?string $inputData, ?string $inputHtml): self
    {
        $instance = self::getInstance();

        $instance->decodeInput($inputData);
        $instance->htmlData = $inputHtml;

        return $instance;
    }

    /**
     * Set content via file
     *
     * @param  string|int $postId
     * @return self
     */
    public static function setContentViaFile(string|int $postId): self
    {
        $instance = self::getInstance();

        $editorData = Storage::get($instance->getContentFilePath($postId, self::DATA_FILE_EXTENSION));
        $editorHtml = Storage::get($instance->getContentFilePath($postId, self::HTML_FILE_EXTENSION));

        return $instance::setContent($editorData, $editorHtml);
    }

    /**
     * Decode incomming data from editor
     *
     * @param  ?string $input
     * @return void
     */
    private function decodeInput(?string $input): void
    {
        $this->originData = $input;

        if (!empty($input) && JsonHelper::isJson($input)) {

            $inputArray = json_decode($input, true);

            // "ops" contains all data as array
            $this->dataList = $inputArray['ops'];
            $this->textAnalyzer = new TextAnalyzer($this->getTexts());
        } else
            $this->textAnalyzer = new TextAnalyzer(null);
    }

    /**
     * Get origin input data
     *
     * @return ?string
     */
    public function getOriginData(): ?string
    {
        return $this->originData;
    }

    /**
     * Get HTML of data
     *
     * @return ?string
     */
    public function getHtml(): ?string
    {
        return $this->htmlData;
    }


    /**
     * Get all data as list
     *
     * @param bool $getAsJson true ? json string : array
     * @return array|string
     */
    public function getDataList(bool $getAsJson = false): array|string
    {
        return $getAsJson ? json_encode($this->dataList) : $this->dataList;
    }

    /**
     * Check if the item is in the text or not
     *
     * @param  mixed $item
     * @return bool
     */
    private function isTextItem(mixed $item): bool
    {
        try {
            return !is_array($item['insert']);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Check if the item is in the image or not
     *
     * @param  mixed $item
     * @return bool
     */
    private function isImageItem(mixed $item): bool
    {
        try {
            return isset($item['insert']['image']);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Check if the item is in the video or not
     *
     * @param  mixed $item
     * @return bool
     */
    private function isVideoItem(mixed $item): bool
    {
        try {
            return isset($item['insert']['video']);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get incoming texts
     *
     * @return array
     */
    public function getTexts(): array
    {
        $texts = [];
        foreach ($this->getDataList() as $item) {

            if ($this->isTextItem($item)) {

                array_push($texts, $item['insert']);
            }
        }

        return $texts;
    }

    /**
     * Get content text
     *
     * @param bool $removeWhiteSpaces
     * @return string
     */
    public function getText(bool $removeWhiteSpaces = true): string
    {
        $text = implode("\n", $this->getTexts());

        return $removeWhiteSpaces ? PregPatternEnum::WhiteSpaces->pregReplace(" ", $text) : $text;
    }

    /**
     * Get incoming images
     *
     * @return array
     */
    public function getImages(): array
    {
        $images = [];
        foreach ($this->getDataList() as $item) {

            if ($this->isImageItem($item)) {

                $image = $item['insert']['image'];

                array_push($images, $image);
            }
        }

        return $images;
    }

    /**
     * Get content file name
     *
     * @param  string|int $postId
     * @param  string $extension DATA_FILE_EXTENSION | HTML_FILE_EXTENSION
     * @return string
     */
    public function getContentFileName(string|int $postId, string $extension): string
    {
        return $postId . $extension;
    }

    /**
     * Get content file path
     *
     * @param  string|int $postId
     * @param  string $extension DATA_FILE_EXTENSION | HTML_FILE_EXTENSION
     * @return string
     */
    public function getContentFilePath(string|int $postId, string $extension): string
    {
        return $this->storagePath . $this->getContentFileName($postId, $extension);
    }


    /**
     * Save the content of the post to a file
     *
     * @param  string|int $postId
     * @return bool
     */
    public function savePostContent(string|int $postId): bool
    {
        $originData = $this->getOriginData();
        $htmlData = $this->getHtml();

        if (empty($originData) || empty($htmlData))
            return true;
        else {

            $dataSaveRes = Storage::put($this->getContentFilePath($postId, self::DATA_FILE_EXTENSION), $originData);
            $htmlSaveRes = Storage::put($this->getContentFilePath($postId, self::HTML_FILE_EXTENSION), $htmlData);

            return $dataSaveRes && $htmlSaveRes;
        }
    }


    /**
     * Get a report on the number of words used.
     *
     * @param  bool $getAsJson true ? JSON string : array
     * @return array
     */
    public function getWordsReport(bool $getAsJson = true): array|string
    {
        return $this->textAnalyzer->getWordsReport($getAsJson);
    }

    public function getWordsCount(bool $getAsJson = false): int
    {
        return $this->textAnalyzer->getWordsCount();
    }
}
