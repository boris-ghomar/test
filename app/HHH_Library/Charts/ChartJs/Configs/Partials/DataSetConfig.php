<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;

/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class DataSetConfig
{
    private string $label = "";
    private array $data = [];
    private GradientColorConfig|string|array $backgroundColor;
    private string|array $borderColor;
    private float $borderWidth;
    private bool $fill;
    private int $pointRadius;
    private int $pointHoverRadius;
    private string $pointBackgroundColor;
    private string $pointBorderColor;


    public function __construct()
    {
        // Defaults

        $this->setBackgroundColor(new GradientColorConfig);
        $this->setBorderWidth(1.5);
        $this->setBorderColor("#F29F67");
        $this->setFill(true);
        $this->setPointRadius(4);
        $this->setPointHoverRadius(8);
        $this->setPointBackgroundColor("#F29F67");
        $this->setPointBorderColor("#252730");
    }

    /**
     * Create config
     *
     * @param  string $chartHtmlContainerId
     * @return array
     */
    public function create(string $chartHtmlContainerId): array
    {
        return [
            'label'                 => $this->getLabel(),
            'data'                  => $this->getData(),
            'borderColor'           => $this->getBorderColor(),
            'borderWidth'           => $this->getBorderWidth(),
            'fill'                  => $this->getFill(),
            'pointRadius'           => $this->getPointRadius(),
            'pointHoverRadius'      => $this->getPointHoverRadius(),
            'pointBackgroundColor'  => $this->getPointBackgroundColor(),
            'pointBorderColor'      => $this->getPointBorderColor(),
            'backgroundColor'       => $this->makeBackgroundColor($chartHtmlContainerId, $this->getBackgroundColor()),
        ];
    }

    /**
     * Make background color
     *
     * @param string $chartHtmlContainerId
     * @param string|array|\App\HHH_Library\Charts\ChartJs\Configs\Partials\GradientColorConfig $backgroundColor
     * @return string|array
     */
    public function makeBackgroundColor(string $chartHtmlContainerId, string|array|GradientColorConfig $backgroundColor): string|array
    {

        if (is_string($backgroundColor)) {
            return $backgroundColor;
        } else if (is_array($backgroundColor)) {

            $colors = [];

            foreach ($backgroundColor as $color) {
                array_push($colors, $this->makeBackgroundColor($chartHtmlContainerId, $color));
            }
            return $colors;
        } else if ($backgroundColor instanceof GradientColorConfig) {

            // Make java script function to return the gradiant color
            $jsColor = "hhh_java(";
            $jsColor .= sprintf("let ctx = document.getElementById('%s');", $chartHtmlContainerId);
            $jsColor .= "let graphGradient = ctx.getContext('2d');";
            $jsColor .= sprintf(
                "let graphGradientBg = graphGradient.createLinearGradient(%s, %s, %s, %s);",
                $backgroundColor->getStartPointX(),
                $backgroundColor->getStartPointY(),
                $backgroundColor->getEndPointX(),
                $backgroundColor->getEndPointY(),
            );

            $jsColor .= sprintf("graphGradientBg.addColorStop(0, '%s');", $backgroundColor->getColorStart());
            $jsColor .= sprintf("graphGradientBg.addColorStop(1, '%s');", $backgroundColor->getColorEnd());

            $jsColor .= "return graphGradientBg;)";

            return $jsColor;
            /*
            // Sample output:

            () => {
                    let ctx = document.getElementById('performanceLine');
                    var graphGradient= ctx.getContext('2d');
                    var graphGradientBg = graphGradient.createLinearGradient(5, 0, 5, 100);
                    graphGradientBg.addColorStop(0, 'rgba(250, 250, 250, 0.98)');
                    graphGradientBg.addColorStop(1, 'rgba(250, 255, 255, 0.82)');
                    return graphGradientBg;
                }
             */
        }

        return "transparent";
    }

    /**
     * Set lable for data set
     *
     * @param  string $label
     * @return self
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get lable
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set data
     *
     * @param  array $data
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set background color
     *
     * @param string|array|\App\HHH_Library\Charts\ChartJs\Configs\Partials\GradientColorConfig $backgroundColor
     * @return self
     */
    public function setBackgroundColor(string|array|GradientColorConfig $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * Get background color
     *
     * @return string|array|\App\HHH_Library\Charts\ChartJs\Configs\Partials\GradientColorConfig
     */
    public function getBackgroundColor(): string|array|GradientColorConfig
    {
        return $this->backgroundColor;
    }

    /**
     * Set border color
     *
     * @param string|array $borderColor
     * @return self
     */
    public function setBorderColor(string|array $borderColor): self
    {
        $this->borderColor = $borderColor;
        return $this;
    }

    /**
     * Get border color
     *
     * @return string|array
     */
    public function getBorderColor(): string|array
    {
        return $this->borderColor;
    }

    /**
     * Set border width
     *
     * @param float $borderWidth
     * @return self
     */
    public function setBorderWidth(float $borderWidth): self
    {
        $this->borderWidth = $borderWidth;
        return $this;
    }

    /**
     * Get border width
     *
     * @return float
     */
    public function getBorderWidth(): float
    {
        return $this->borderWidth;
    }

    /**
     * Set fill
     *
     * @param bool $fill
     * @return self
     */
    public function setFill(bool $fill): self
    {
        $this->fill = $fill;
        return $this;
    }

    /**
     * Get fill
     *
     * @return bool
     */
    public function getFill(): bool
    {
        return $this->fill;
    }

    /**
     * Set point radius
     *
     * @param int $pointRadius
     * @return self
     */
    public function setPointRadius(int $pointRadius): self
    {
        $this->pointRadius = $pointRadius;
        return $this;
    }

    /**
     * Get point radius
     *
     * @return int
     */
    public function getPointRadius(): int
    {
        return $this->pointRadius;
    }

    /**
     * Set point hover radius
     *
     * @param int $pointHoverRadius
     * @return self
     */
    public function setPointHoverRadius(int $pointHoverRadius): self
    {
        $this->pointHoverRadius = $pointHoverRadius;
        return $this;
    }

    /**
     * Get point hover radius
     *
     * @return int
     */
    public function getPointHoverRadius(): int
    {
        return $this->pointHoverRadius;
    }

    /**
     * Set point point background color
     *
     * @param string $pointBackgroundColor
     * @return self
     */
    public function setPointBackgroundColor(string $pointBackgroundColor): self
    {
        $this->pointBackgroundColor = $pointBackgroundColor;
        return $this;
    }

    /**
     * Get point background color
     *
     * @return string
     */
    public function getPointBackgroundColor(): string
    {
        return $this->pointBackgroundColor;
    }

    /**
     * Set point border color
     *
     * @param string $pointBorderColor
     * @return self
     */
    public function setPointBorderColor(string $pointBorderColor): self
    {
        $this->pointBorderColor = $pointBorderColor;
        return $this;
    }

    /**
     * Get point border color
     *
     * @return string
     */
    public function getPointBorderColor(): string
    {
        return $this->pointBorderColor;
    }
}
