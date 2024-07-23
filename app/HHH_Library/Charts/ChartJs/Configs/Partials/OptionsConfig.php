<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;


/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class OptionsConfig
{

    private bool $responsive = true;
    private bool $maintainAspectRatio = false;
    private array $elements = [];
    private array $scales = [];
    private array $plugins = [];

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults
        $this->setResponsive(true);
    }

    /**
     * Create config
     *
     * @return array
     */
    public function create(): array
    {
        return [
            'responsive'            => $this->getResponsive(),
            'maintainAspectRatio'   => $this->getMaintainAspectRatio(),
            'elements'              => $this->getElements(),
            'scales'                => $this->getScales(),
            'plugins'               => $this->getPlugins(),
        ];
    }

    /**
     * Set responsive
     *
     * @param bool $responsive
     * @return self
     */
    public function setResponsive(bool $responsive): self
    {
        $this->responsive = $responsive;
        return $this;
    }

    /**
     * Get responsive
     *
     * @return bool
     */
    public function getResponsive(): bool
    {
        return $this->responsive;
    }

    /**
     * Set maintain aspect ratio
     *
     * @param bool $maintainAspectRatio
     * @return self
     */
    public function setMaintainAspectRatio(bool $maintainAspectRatio): self
    {
        $this->maintainAspectRatio = $maintainAspectRatio;
        return $this;
    }

    /**
     * Get maintain aspect ratio
     *
     * @return bool
     */
    public function getMaintainAspectRatio(): bool
    {
        return $this->maintainAspectRatio;
    }

    /**
     * Add element line
     *
     * @param  \App\HHH_Library\Charts\ChartJs\Configs\Partials\LineConfig $lineConfig
     * @return self
     */
    public function addElementLine(LineConfig $lineConfig): self
    {
        $this->elements['line'] = $lineConfig->create();
        return $this;
    }

    /**
     * Get elements
     *
     * @return array
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Add scale X
     *
     * @param \App\HHH_Library\Charts\ChartJs\Configs\Partials\ScaleConfig $scaleConfig
     * @return self
     */
    public function addScaleX(ScaleConfig $scaleConfig): self
    {
        $this->scales['x'] = $scaleConfig->create();
        return $this;
    }

    /**
     * Add scale Y
     *
     * @param \App\HHH_Library\Charts\ChartJs\Configs\Partials\ScaleConfig $scaleConfig
     * @return self
     */
    public function addScaleY(ScaleConfig $scaleConfig): self
    {
        $this->scales['y'] = $scaleConfig->create();
        return $this;
    }

    /**
     * Get scales
     *
     * @return array
     */
    public function getScales(): array
    {
        return $this->scales;
    }

    /**
     * Add legend plugin
     *
     * @param  \App\HHH_Library\Charts\ChartJs\Configs\Partials\LegendConfig $legendConfig
     * @return self
     */
    public function addPluginLegend(LegendConfig $legendConfig): self
    {
        $this->plugins['legend'] = $legendConfig->create();
        return $this;
    }

    /**
     * Add tooltips plugin
     *
     * @param  \App\HHH_Library\Charts\ChartJs\Configs\Partials\LegendConfig $legendConfig
     * @return self
     */
    public function addPluginTooltips(TooltipsConfig $tooltipsConfig): self
    {
        $this->plugins['tooltips'] = $tooltipsConfig->create();
        return $this;
    }

    /**
     * Get plugins
     *
     * @return array
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }
}
