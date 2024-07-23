<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;


/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class ScaleConfig
{

    private TitleConfig $title;
    private BorderConfig $border;
    private GridConfig $grid;
    private TicksConfig $ticks;
    private ?int $min = null;
    private ?int $max = null;


    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults
    }

    /**
     * Create config
     *
     * @return array
     */
    public function create(): array
    {
        $config = [
            'title'     => $this->getTitle()->create(),
            'border'    => $this->getBorder()->create(),
            'grid'      => $this->getGrid()->create(),
            'ticks'     => $this->getTicks()->create(),
        ];

        if (!is_null($this->getMin()))
            $config['min'] = $this->getMin();

        if (!is_null($this->getMax()))
            $config['max'] = $this->getMax();

        return $config;
    }

    /**
     * Set title
     *
     * @param \App\HHH_Library\Charts\ChartJs\Configs\Partials\TitleConfig $title
     * @return self
     */
    public function setTitle(TitleConfig $titleConfig): self
    {
        $this->title = $titleConfig;
        return $this;
    }

    /**
     * Get title
     *
     * @return \App\HHH_Library\Charts\ChartJs\Configs\Partials\TitleConfig
     */
    public function getTitle(): TitleConfig
    {
        return $this->title;
    }

    /**
     * Set border
     *
     * @param \App\HHH_Library\Charts\ChartJs\Configs\Partials\BorderConfig $borderConfig
     * @return self
     */
    public function setBorder(BorderConfig $borderConfig): self
    {
        $this->border = $borderConfig;
        return $this;
    }

    /**
     * Get border
     *
     * @return \App\HHH_Library\Charts\ChartJs\Configs\Partials\BorderConfig
     */
    public function getBorder(): BorderConfig
    {
        return $this->border;
    }

    /**
     * Set grid
     *
     * @param \App\HHH_Library\Charts\ChartJs\Configs\Partials\GridConfig $gridConfig
     * @return self
     */
    public function setGrid(GridConfig $gridConfig): self
    {
        $this->grid = $gridConfig;
        return $this;
    }

    /**
     * Get grid
     *
     * @return \App\HHH_Library\Charts\ChartJs\Configs\Partials\GridConfig
     */
    public function getGrid(): GridConfig
    {
        return $this->grid;
    }

    /**
     * Set ticks
     *
     * @param \App\HHH_Library\Charts\ChartJs\Configs\Partials\TicksConfig $ticksConfig
     * @return self
     */
    public function setTicks(TicksConfig $ticksConfig): self
    {
        $this->ticks = $ticksConfig;
        return $this;
    }

    /**
     * Get ticks
     *
     * @return \App\HHH_Library\Charts\ChartJs\Configs\Partials\TicksConfig
     */
    public function getTicks(): TicksConfig
    {
        return $this->ticks;
    }

    /**
     * Set minimum
     *
     * @param  ?int $min null: disable
     * @return self
     */
    public function setMin(?int $min): self
    {
        $this->min = $min;
        return $this;
    }

    /**
     * Get minimum
     *
     * @return ?int null: disable
     */
    public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * Set maximum
     *
     * @param  ?int $max null: disable
     * @return self
     */
    public function setMax(?int $max): self
    {
        $this->max = $max;
        return $this;
    }

    /**
     * Get maximum
     *
     * @return ?int null: disable
     */
    public function getMax(): ?int
    {
        return $this->max;
    }
}
