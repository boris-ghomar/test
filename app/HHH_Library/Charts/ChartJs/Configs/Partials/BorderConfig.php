<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;

/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class BorderConfig
{

    private bool $display;
    private string $color;
    private float $width;
    private int $zIndex;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults

        $this->setDisplay(true);
        $this->setColor('#605f61');
        $this->setWidth(1);
        $this->setZIndex(0);
    }

    /**
     * Create config
     *
     * @return array
     */
    public function create(): array
    {
        return [
            'display'   => $this->getDisplay(),
            'color'     => $this->getColor(),
            'width'     => $this->getWidth(),
            'z'         => $this->getZIndex(),
        ];
    }

    /**
     * Set display
     *
     * @param  bool $display
     * @return self
     */
    public function setDisplay(bool $display): self
    {
        $this->display = $display;

        return $this;
    }

    /**
     * Get display
     *
     * @return bool
     */
    public function getDisplay(): bool
    {
        return $this->display;
    }

    /**
     * Set color
     *
     * @param  string $color
     * @return self
     */
    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Set width
     *
     * @param  float $width
     * @return self
     */
    public function setWidth(float $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get width
     *
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * Set zIndex
     *
     * @param  int $zIndex
     * @return self
     */
    public function setZIndex(int $zIndex): self
    {
        $this->zIndex = $zIndex;
        return $this;
    }

    /**
     * Get zIndex
     *
     * @return int
     */
    public function getZIndex(): int
    {
        return $this->zIndex;
    }
}
