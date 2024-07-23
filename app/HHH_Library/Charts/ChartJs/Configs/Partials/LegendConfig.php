<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;


/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class LegendConfig
{
    const POSITION_TOP = "top", POSITION_BOTTOM = "bottom", POSITION_RIGHT = "right", POSITION_LEFT = "left";

    private bool $display;
    private LabelsConfig $labels;
    private string $position;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults

        $this->setDisplay(true);
        $this->setPosition(self::POSITION_TOP);
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
            'labels'    => $this->getLabels()->create(),
            'position'  => $this->getPosition(),
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
     * Set labels
     *
     * @param  \App\HHH_Library\Charts\ChartJs\Configs\Partials\LabelsConfig $labelsConfig
     * @return self
     */
    public function setLabels(LabelsConfig $labelsConfig): self
    {
        $this->labels = $labelsConfig;
        return $this;
    }

    /**
     * Get labels
     *
     * @return \App\HHH_Library\Charts\ChartJs\Configs\Partials\LabelsConfig
     */
    public function getLabels(): LabelsConfig
    {
        return $this->labels;
    }

    /**
     * Set position
     *
     * @param  string $position use const values (POSITION_TOP | POSITION_BOTTOM | POSITION_RIGHT | POSITION_LEFT)
     * @return self
     */
    public function setPosition(string $position): self
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return string const values (POSITION_TOP | POSITION_BOTTOM | POSITION_RIGHT | POSITION_LEFT)
     */
    public function getPosition(): string
    {
        return $this->position;
    }
}
