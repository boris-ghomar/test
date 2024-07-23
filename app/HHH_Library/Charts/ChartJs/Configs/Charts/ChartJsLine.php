<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Charts;

use App\Enums\General\WeekDayEnum;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\BorderConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\DataConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\GridConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\LabelsConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\LegendConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\LineConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\OptionsConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\ScaleConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\Themes\DataSetConfigThemesEnum;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\TicksConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\TitleConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\TooltipsConfig;
use App\HHH_Library\general\php\Enums\LocaleEnum;

/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */

class ChartJsLine extends SuperChartJs
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
        $this->setChartType(self::CHART_TYPE_LINE);
    }

    /**
     * Load sample chart
     *
     * @return self
     */
    public function loadSample(): self
    {
        $chartHtmlContainerId = $this->getHtmlContainerId();

        $currentWeekDataSet = DataSetConfigThemesEnum::Orange->create(__('chart.CurrentWeek'), [25, 12, 0, 13]);
        $lastWeekDataSet = DataSetConfigThemesEnum::Gray->create(__('chart.LastWeek'), [12, 0, 1, 2, 15, 20, 2]);


        $dataConfig = (new DataConfig())
            ->setLabels(array_values(WeekDayEnum::getCollectionList(false, false, LocaleEnum::Persian)))
            ->addDataSet($chartHtmlContainerId, $currentWeekDataSet)
            ->addDataSet($chartHtmlContainerId, $lastWeekDataSet);

        $scaleX = (new ScaleConfig())
            ->setTitle(
                (new TitleConfig())
                    ->setDisplay(true)
                    ->setText(__('chart.Day'))
            )
            ->setBorder(new BorderConfig)
            ->setGrid((new GridConfig)->setDisplay(false))
            ->setTicks((new TicksConfig)->setMaxTicksLimit(7));

        $scaleY = (new ScaleConfig())
            ->setTitle(
                (new TitleConfig())
                    ->setDisplay(true)
                    ->setText(__('chart.Total'))
            )
            ->setBorder(new BorderConfig)
            ->setGrid(new GridConfig)
            ->setTicks((new TicksConfig)->setMaxTicksLimit(5));

        $legend = (new LegendConfig)
            ->setDisplay(true)
            ->setLabels(new LabelsConfig);

        $options = (new OptionsConfig)
            ->addElementLine((new LineConfig)->setTension(0.4))
            ->addScaleX($scaleX)
            ->addScaleY($scaleY)
            ->addPluginLegend($legend)
            ->addPluginTooltips(new TooltipsConfig);

        $chartConfig = (new self($chartHtmlContainerId))
            ->setContainerCssCols(12)
            ->setCardTitle(__('thisApp.Site.ReferralPanel.ReferredPerformanceChart.CardTitle'))
            ->setCardSubtitle(__('thisApp.Site.ReferralPanel.ReferredPerformanceChart.CardSubtitle'))
            ->setDataConfig($dataConfig)
            ->setOptions($options);

        return $chartConfig;
    }
}
