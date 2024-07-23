<?php

namespace App\Enums\Resources\Interfaces;

interface ImageConfigInterface extends FileConfigInterface
{

    /**
     * Get default images path.
     * System default image path.
     *
     * @return string
     */
    public function defaultPath(): string;

    /**
     * Get default images name.
     * When there is no image, the app uses these images.
     *
     * @return string
     */
    public function defaultImage(): string;

    /**
     * Get full path of default image.
     *
     * @return string
     */
    public function defaultImagePath(): string;

    /**
     * Get resize width of image in pixel.
     *
     * When it is necessary to change the dimensions of the image,
     * the app uses these dimensions.
     *
     * @return int
     */
    public function resizeWidth(): int;

    /**
     * Get resize height of image in pixel.
     *
     * When it is necessary to change the dimensions of the image,
     * the app uses these dimensions.
     *
     * @return int
     */
    public function resizeHeight(): int;

    /**
     * Get resize dpi of image.
     *
     * When it is necessary to change the dimensions of the image,
     * the app uses these dimensions.
     *
     * @return int
     */
    public function resizeDpi(): int;

    /**
     * Get convert type of image.
     *
     * When it needs to change the image type,
     * the app will convert the image to this type of image.
     *
     * @return string
     */
    public function convertType(): string;

    /**
     * Minimum acceptable width of image in pixel.
     *
     *
     * @return int
     */
    public function minWidth(): int;

    /**
     * Minimum acceptable height of image in pixel.
     *
     *
     * @return int
     */
    public function minHeight(): int;

    /**
     * Minimum acceptable height of image in kb.
     *
     *
     * @return int
     */
    public function minSize(): int;

    /**
     * Maximum acceptable width of image in pixel.
     *
     *
     * @return int
     */
    public function maxWidth(): int;

    /**
     * Maximum acceptable height of image in pixel.
     *
     *
     * @return int
     */
    public function maxHeight(): int;

    /**
     * Maximum acceptable height of image in kb.
     *
     *
     * @return int
     */
    public function maxSize(): int;
}
