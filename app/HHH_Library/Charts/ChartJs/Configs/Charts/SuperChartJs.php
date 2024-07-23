<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Charts;

use App\HHH_Library\Charts\ChartJs\Configs\Partials\DataConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\OptionsConfig;

/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */

abstract class SuperChartJs
{
    // Chart types
    const
        CHART_TYPE_LINE         = "line",
        CHART_TYPE_BAR          = "bar",
        CHART_TYPE_BUBBLE       = "bubble", // 3D Needs to config (Not very efficient)
        CHART_TYPE_DOUGHNUT     = "doughnut",
        CHART_TYPE_PIE          = "pie",
        CHART_TYPE_POLAR_AREA   = "polarArea", // Needs to config
        CHART_TYPE_POLAR_RADAR  = "radar", // Needs to config
        CHART_TYPE_SCATTER      = "scatter"; // Needs to config


    /*************************** implements ***************************/
    /*************************** implements END ***************************/


    private string $htmlContainerId = "";

    private int $containerCssCols = 12; // Based on bootstrap CSS row->cols between 5 to 12
    private string $cardTitle = "";
    private string $cardSubtitle = "";
    private string $cardFooter = "";

    private string $chartType = "";
    private DataConfig $dataConfig;
    private OptionsConfig $optionsConfig;


    /**
     * Create html view tags for chart
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function createView()
    {
        $viewParamData = [
            'containerId'       => $this->getHtmlContainerId(),
            'containerCssCols'  => $this->getContainerCssCols(),
            'cardTitle'         => $this->getCardTitle(),
            'cardSubtitle'      => $this->getCardSubtitle(),
            'cardFooter'        => $this->getCardFooter(),
        ];

        return view("HHH_Library::Charts/ChartJs/ChartJsView", $viewParamData);
    }

    /**
     * Create required script for chart data
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function createScript()
    {
        $data =  [
            'containerId' => $this->getHtmlContainerId(),
            'chartConfig' => json_encode($this->getJsConfigs()),
        ];

        return view("HHH_Library::Charts/ChartJs/ChartJsScript", $data);
    }

    /**
     * Get javascript configs of ChartJs
     *
     * @return array
     */
    public function getJsConfigs(): array
    {
        return [
            'type'      => $this->getChartType(),
            'data'      => $this->getDataConfig()->create(),
            'options'   => $this->getOptions()->create(),
        ];
    }

    /**
     * Set HTML container ID
     *
     * @param  string $containerId
     * @return self
     */
    public function setHtmlContainerId(string $htmlContainerId): self
    {
        if (!empty($htmlContainerId))
            $this->htmlContainerId = $htmlContainerId;

        return $this;
    }

    /**
     * Get HTML container ID
     *
     * @return string
     */
    public function getHtmlContainerId(): string
    {
        return $this->htmlContainerId;
    }

    /**
     * Set container CSS cols
     * Based on bootstrap CSS row->cols
     *
     * @param  int $containerCssCols Between 5 to 12
     * @return self
     */
    public function setContainerCssCols(int $containerCssCols): self
    {
        if ($containerCssCols < 5)
            $containerCssCols = 5;

        if ($containerCssCols > 12)
            $containerCssCols = 12;

        $this->containerCssCols = $containerCssCols;

        return $this;
    }

    /**
     * Get container CSS cols
     * Based on bootstrap CSS row->cols
     *
     * @return int
     */
    public function getContainerCssCols(): int
    {
        return $this->containerCssCols;
    }

    /**
     * Set card title
     *
     * @param  ?string $cardTitle
     * @return self
     */
    public function setCardTitle(?string $cardTitle): self
    {
        $this->cardTitle = is_null($cardTitle) ? "" : $cardTitle;
        return $this;
    }

    /**
     * Get card title
     *
     * @return string
     */
    public function getCardTitle(): string
    {
        return $this->cardTitle;
    }

    /**
     * Set card subtitle
     *
     * @param  ?string $cardSubtitle
     * @return self
     */
    public function setCardSubtitle(?string $cardSubtitle): self
    {
        $this->cardSubtitle = is_null($cardSubtitle) ? "" : $cardSubtitle;

        return $this;
    }

    /**
     * Get card subtitle
     *
     * @return string
     */
    public function getCardSubtitle(): string
    {
        return $this->cardSubtitle;
    }

    /**
     * Set card footer
     *
     * @param  ?string $cardFooter
     * @return self
     */
    public function setCardFooter(?string $cardFooter): self
    {
        $this->cardFooter = is_null($cardFooter) ? "" : $cardFooter;

        return $this;
    }

    /**
     * Get card footer
     *
     * @return string
     */
    public function getCardFooter(): string
    {
        return $this->cardFooter;
    }

    /**
     * Set chart type
     *
     * @param  ?string $chartType use const values (self::CHART_TYPE_LINE | self::CHART_TYPE_BAR | ...)
     * @return self
     */
    public function setChartType(?string $chartType = self::CHART_TYPE_LINE): self
    {
        $this->chartType = empty($chartType) ? self::CHART_TYPE_LINE : $chartType;
        return $this;
    }

    /**
     * Get chart type
     *
     * @return string
     */
    public function getChartType(): string
    {
        return $this->chartType;
    }

    /**
     * Set data config
     *
     * @param  \App\HHH_Library\Charts\ChartJs\Configs\Partials\DataConfig $dataConfig
     * @return self
     */
    public function setDataConfig(DataConfig $dataConfig): self
    {
        $this->dataConfig = $dataConfig;
        return $this;
    }

    /**
     * Get data config
     *
     * @return \App\HHH_Library\Charts\ChartJs\Configs\Partials\DataConfig
     */
    public function getDataConfig(): DataConfig
    {
        return $this->dataConfig;
    }

    /**
     * Set options
     *
     * @param  \App\HHH_Library\Charts\ChartJs\Configs\Partials\OptionsConfig $optionsConfig
     * @return self
     */
    public function setOptions(OptionsConfig $optionsConfig): self
    {
        $this->optionsConfig = $optionsConfig;
        return $this;
    }

    /**
     * Get options
     *
     * @return \App\HHH_Library\Charts\ChartJs\Configs\Partials\OptionsConfig
     */
    public function getOptions(): OptionsConfig
    {
        return $this->optionsConfig;
    }
}
