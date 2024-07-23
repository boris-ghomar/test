<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;


/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class TicksConfig
{

    private bool $display;
    private bool $beginAtZero;
    private bool $autoSkip;
    private int $stepSize;
    private int $maxTicksLimit;
    private string $color;
    private FontConfig $fontConfig;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults

        $this->setDisplay(true);
        $this->setBeginAtZero(false);
        $this->setAutoSkip(true);
        $this->setStepSize(1);
        $this->setMaxTicksLimit(7);
        $this->setColor("#808191");
        $this->setFontConfig(new FontConfig);
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
            'beginAtZero'   => $this->getBeginAtZero(),
            'autoSkip'      => $this->getAutoSkip(),
            'stepSize'      => $this->getStepSize(),
            'maxTicksLimit' => $this->getMaxTicksLimit(),
            'color'         => $this->getColor(),
            'font'          => $this->getFontConfig()->create(),
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
     * Set begin at zero
     *
     * @param  bool $beginAtZero
     * @return self
     */
    public function setBeginAtZero(bool $beginAtZero): self
    {
        $this->beginAtZero = $beginAtZero;
        return $this;
    }

    /**
     * Get begin at zero
     *
     * @return bool
     */
    public function getBeginAtZero(): bool
    {
        return $this->beginAtZero;
    }

    /**
     * Set auto skip
     *
     * @param  bool $autoSkip
     * @return self
     */
    public function setAutoSkip(bool $autoSkip): self
    {
        $this->autoSkip = $autoSkip;
        return $this;
    }

    /**
     * Get auto skip
     *
     * @return bool
     */
    public function getAutoSkip(): bool
    {
        return $this->autoSkip;
    }

    /**
     * Set step size
     *
     * @param  int $stepSize
     * @return self
     */
    public function setStepSize(int $stepSize): self
    {
        $this->stepSize = $stepSize;
        return $this;
    }

    /**
     * Get max step size
     *
     * @return int
     */
    public function getStepSize(): int
    {
        return $this->stepSize;
    }

    /**
     * Set max ticks limit
     *
     * @param  int $maxTicksLimit
     * @return self
     */
    public function setMaxTicksLimit(int $maxTicksLimit): self
    {
        $this->maxTicksLimit = $maxTicksLimit;
        return $this;
    }

    /**
     * Get max ticks limit
     *
     * @return int
     */
    public function getMaxTicksLimit(): int
    {
        return $this->maxTicksLimit;
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
     * Set font config
     *
     * @param  \App\HHH_Library\Charts\ChartJs\ConfigCreators\FontConfig $fontConfig
     * @return self
     */
    public function setFontConfig(FontConfig $fontConfig): self
    {
        $this->fontConfig = $fontConfig;
        return $this;
    }

    /**
     * Get font config
     *
     * @return \App\HHH_Library\Charts\ChartJs\ConfigCreators\FontConfig
     */
    public function getFontConfig(): FontConfig
    {
        return $this->fontConfig;
    }
}
