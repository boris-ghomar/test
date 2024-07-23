<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;


/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class TitleConfig
{

    private bool $display;
    private string $text = "";
    private string $color;
    private FontConfig $fontConfig;
    private array $padding;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults

        $this->setDisplay(true);
        $this->setColor("#6d6a6a");
        $this->setFontConfig(new FontConfig);
        $this->setPadding();
    }

    /**
     * Create config
     *
     * @return array
     */
    public function create(): array
    {
        return [
            'display'   => empty($this->getText()) ? false : $this->getDisplay(),
            'text'      => $this->getText(),
            'color'     => $this->getColor(),
            'font'      => $this->getFontConfig()->create(),
            'padding'   => $this->getPadding(),
        ];
    }

    /**
     * Set display
     *
     * @param  bool $display
     * @return self
     */
    public function setDisplay(bool $display): self
    {
        $this->display = $display;
        return $this;
    }

    /**
     * Get display
     *
     * @return bool
     */
    public function getDisplay(): bool
    {
        return $this->display;
    }

    /**
     * Set text
     *
     * @param  string $text
     * @return self
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set color
     *
     * @param  string $color
     * @return self
     */
    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Set font config
     *
     * @param  \App\HHH_Library\Charts\ChartJs\ConfigCreators\FontConfig $fontConfig
     * @return self
     */
    public function setFontConfig(FontConfig $fontConfig): self
    {
        $this->fontConfig = $fontConfig;
        return $this;
    }

    /**
     * Get font config
     *
     * @return \App\HHH_Library\Charts\ChartJs\ConfigCreators\FontConfig
     */
    public function getFontConfig(): FontConfig
    {
        return $this->fontConfig;
    }

    /**
     * Set padding
     *
     * @param  int $top
     * @param  int $left
     * @param  int $right
     * @param  int $bottom
     * @return self
     */
    public function setPadding(int $top = 0, int $left = 0, int $right = 0, int $bottom = 0): self
    {
        $this->padding = [
            'top'       => $top,
            'left'      => $left,
            'right'     => $right,
            'bottom'    => $bottom
        ];

        return $this;
    }

    /**
     * Get padding
     *
     * @return array
     */
    public function getPadding(): array
    {
        return $this->padding;
    }
}
