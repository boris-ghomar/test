<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;


/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class TooltipsConfig
{

    private string $backgroundColor;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults

        $this->setBackgroundColor('rgba(31, 59, 179, 1)');
    }

    /**
     * Create config
     *
     * @return array
     */
    public function create(): array
    {
        return [
            'backgroundColor'   => $this->getBackgroundColor(),
        ];
    }

    /**
     * Set background color
     *
     * @param  string $backgroundColor
     * @return self
     */
    public function setBackgroundColor(string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * Get background color
     *
     * @return string
     */
    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }
}
