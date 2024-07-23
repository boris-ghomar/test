<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;


/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class LineConfig
{

    private float $tension;


    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults

        $this->setTension(0.4);
    }

    /**
     * Create config
     *
     * @return array
     */
    public function create(): array
    {
        return [
            'tension'   => $this->getTension(),
        ];
    }

    /**
     * Set tension
     *
     * @param  float $tension
     * @return self
     */
    public function setTension(float $tension): self
    {
        $this->tension = $tension;
        return $this;
    }

    /**
     * Get tension
     *
     * @return float
     */
    public function getTension(): float
    {
        return $this->tension;
    }
}
