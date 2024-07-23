<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials\Themes;

use App\HHH_Library\Charts\ChartJs\Configs\Partials\DataSetConfig;
use App\HHH_Library\Charts\ChartJs\Configs\Partials\GradientColorConfig;
use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum DataSetConfigThemesEnum
{
    use EnumActions;

    case Orange;
    case Blue;
    case Green;
    case Yellow;
    case Red;
    case Purple;
    case Gray;


    /**
     * Create config
     *
     * @param ?string $label
     * @param ?array $data
     * @return \App\HHH_Library\Charts\ChartJs\Configs\Partials\DataSetConfig
     */
    public function create(?string $label, ?array $data): DataSetConfig
    {
        return $this->createThemeDataSetConfig($label, $data);
    }

    /**
     * Get theme color set
     *
     * @return \App\HHH_Library\Charts\ChartJs\Configs\Partials\Themes\ThemesColorSetEnum
     */
    public function getThemeColorSet(): ThemesColorSetEnum
    {
        $themeColorSet = ThemesColorSetEnum::getCase($this->name);

        return is_null($themeColorSet) ? ThemesColorSetEnum::Orange : $themeColorSet;
    }

    /**
     * Create theme data set config
     *
     * @param ?string $label
     * @param ?array $data
     * @return \App\HHH_Library\Charts\ChartJs\Configs\Partials\DataSetConfig
     */
    private function createThemeDataSetConfig(?string $label, ?array $data): DataSetConfig
    {
        $themeColorSet = $this->getThemeColorSet();

        $config = new DataSetConfig();

        $gradientColorConfig = (new GradientColorConfig)
            ->setStartPoint(5, 0)
            ->setEndPoint(5, 100)
            ->setColorStart($themeColorSet->getGradiantColorStart())
            ->setColorEnd($themeColorSet->getGradiantColorEnd());

        $config->setBackgroundColor($gradientColorConfig)
            ->setBorderWidth(1.5)
            ->setFill(true)
            ->setPointRadius(4)
            ->setPointHoverRadius(8)
            ->setPointBorderColor($themeColorSet->getPointBorderColor())
            ->setBorderColor($themeColorSet->getBorderColor())
            ->setPointBackgroundColor($themeColorSet->getPointBackgroundColor());

        if (!is_null($label))
            $config->setLabel($label);

        if (!is_null($data))
            $config->setData($data);

        return $config;
    }
}
