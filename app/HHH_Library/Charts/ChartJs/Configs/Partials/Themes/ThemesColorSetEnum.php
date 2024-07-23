<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials\Themes;

use App\HHH_Library\general\php\traits\Enums\EnumActions;

enum ThemesColorSetEnum
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
     * Get gradiant color start
     *
     * @return array
     */
    private function getColorRgbArray(): array
    {
        return match ($this) {
            self::Orange    => [242, 159, 103],
            self::Blue      => [54, 162, 235],
            self::Green     => [75, 192, 108],
            self::Yellow    => [255, 206, 86],
            self::Red       => [255, 99, 132],
            self::Purple    => [153, 102, 255],
            self::Gray      => [109, 110, 110],

            default => self::Orange->getColorRgbArray()
        };
    }

    /**
     * Get gradiant color start
     *
     * @return string
     */
    public function getColor(): string
    {
        $colorArray = $this->getColorRgbArray();

        return sprintf('rgb(%s)', implode(",", $colorArray));
    }

    /**
     * Get gradiant color start
     *
     * @return string
     */
    public function getGradiantColorStart(): string
    {
        $colorArray = $this->getColorRgbArray();
        array_push($colorArray, '0.4');

        return sprintf('rgba(%s)', implode(",", $colorArray));
    }

    /**
     * Get gradiant color end
     *
     * @return string
     */
    public function getGradiantColorEnd(): string
    {
        $colorArray = $this->getColorRgbArray();
        array_push($colorArray, '0.07');

        return sprintf('rgba(%s)', implode(",", $colorArray));
    }

    /**
     * Get border color
     *
     * @return string
     */
    public function getBorderColor(): string
    {
        $colorArray = $this->getColorRgbArray();
        array_push($colorArray, '0.8');

        return sprintf('rgba(%s)', implode(",", $colorArray));
    }

    /**
     * Get point border color
     *
     * @return string
     */
    public function getPointBorderColor(): string
    {
        return "#252730";
    }

    /**
     * Get point background color
     *
     * @return string
     */
    public function getPointBackgroundColor(): string
    {
        return match ($this) {
            self::Orange    => $this->getBorderColor(),
            self::Blue      => $this->getBorderColor(),
            self::Green     => $this->getBorderColor(),
            self::Yellow    => $this->getBorderColor(),
            self::Red       => $this->getBorderColor(),
            self::Purple    => $this->getBorderColor(),
            self::Gray      => $this->getBorderColor(),

            default => self::Orange->getPointBackgroundColor()
        };
    }
}
