<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;


/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class GridConfig
{

    private bool $display;
    private bool $drawTicks;
    private string $color;
    private string $zeroLineColor;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults

        $this->setDisplay(true);
        $this->setDrawTicks(false);
        $this->setColor("#383A42");
        $this->setZeroLineColor("#383A42");
    }

    /**
     * Create config
     *
     * @return array
     */
    public function create(): array
    {
        return [
            'display'       => $this->getDisplay(),
            'drawTicks'     => $this->getDrawTicks(),
            'color'         => $this->getColor(),
            'zeroLineColor' => $this->getZeroLineColor(),
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
     * Set draw ticks
     *
     * @param  bool $drawTicks
     * @return self
     */
    public function setDrawTicks(bool $drawTicks): self
    {
        $this->drawTicks = $drawTicks;
        return $this;
    }

    /**
     * Get draw ticks
     *
     * @return bool
     */
    public function getDrawTicks(): bool
    {
        return $this->drawTicks;
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
     * Set zero line color
     *
     * @param  string $color
     * @return self
     */
    public function setZeroLineColor(string $color): self
    {
        $this->zeroLineColor = $color;
        return $this;
    }

    /**
     * Get zero line color
     *
     * @return string
     */
    public function getZeroLineColor(): string
    {
        return $this->zeroLineColor;
    }
}
