<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Charts;

use App\HHH_Library\Charts\ChartJs\Configs\Partials\DataConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\DataSetConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\LabelsConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\LegendConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\OptionsConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\Themes\ThemesColorSetEnum;

/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */

class ChartJsPie extends SuperChartJs
{

    /**
     * __construct
     *
     * @param  string $containerId
     * @return void
     */
    public function __construct(string $htmlContainerId = "ChartJsContainer")
    {
        $this->setHtmlContainerId($htmlContainerId);
        $this->setChartType(self::CHART_TYPE_PIE);
    }

    /**
     * Load sample chart
     *
     * @return self
     */
    public function loadSample(): self
    {
        $chartHtmlContainerId = $this->getHtmlContainerId();

        $soldTheme = ThemesColorSetEnum::Green;
        $returnedTheme = ThemesColorSetEnum::Red;
        $changedTheme = ThemesColorSetEnum::Blue;

        $dataset = (new DataSetConfig)
            ->setLabel('Total')
            ->setData([45, 10, 15])
            ->setBackgroundColor([$soldTheme->getGradiantColorStart(), $returnedTheme->getGradiantColorStart(), $changedTheme->getGradiantColorStart()])
            ->setBorderColor([$soldTheme->getBorderColor(), $returnedTheme->getBorderColor(), $changedTheme->getBorderColor()]);

        $dataConfig = (new DataConfig())
            ->setLabels(['Sold', 'Returned', 'Changed'])
            ->addDataSet($chartHtmlContainerId, $dataset);

        $legend = (new LegendConfig)
            ->setDisplay(true)
            ->setPosition(LegendConfig::POSITION_TOP)
            ->setLabels(new LabelsConfig);

        $options = (new OptionsConfig)
            ->addPluginLegend($legend);

        $chartConfig = (new self($chartHtmlContainerId))
            ->setContainerCssCols(12)
            ->setCardTitle(__('thisApp.Site.ReferralPanel.ReferredPerformanceChart.CardTitle'))
            ->setCardSubtitle(__('thisApp.Site.ReferralPanel.ReferredPerformanceChart.CardSubtitle'))
            ->setDataConfig($dataConfig)
            ->setOptions($options);

        return $chartConfig;
    }
}
