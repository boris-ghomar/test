<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;


/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class LabelsConfig
{

    private string $color;
    private FontConfig $font;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults
        $this->setColor("gray");
        $this->setFont(new FontConfig);
    }

    /**
     * Create config
     *
     * @return array
     */
    public function create(): array
    {
        return [
            'color' => $this->getColor(),
            'font'  => $this->getFont()->create(),
        ];
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
     * Set font
     *
     * @param  \App\HHH_Library\Charts\ChartJs\Configs\Partials\FontConfig $font
     * @return self
     */
    public function setFont(FontConfig $font): self
    {
        $this->font = $font;
        return $this;
    }

    /**
     * Get font
     *
     * @return \App\HHH_Library\Charts\ChartJs\Configs\Partials\FontConfig
     */
    public function getFont(): FontConfig
    {
        return $this->font;
    }
}
