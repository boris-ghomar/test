<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;

/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class FontConfig
{
    // Font styles
    const
        FONT_STYLE_NORMAL   = "normal",
        FONT_STYLE_ITALIC   = "italic",
        FONT_STYLE_OBIQUE   = "oblique",
        FONT_STYLE_INITIAL  = "initial",
        FONT_STYLE_INHERIT  = "inherit";

    // Font weights
    const
        FONT_WEIGHT_LIGHTER = "lighter",
        FONT_WEIGHT_NORMAL  = "normal",
        FONT_WEIGHT_BOLD    = "bold",
        FONT_WEIGHT_BOLDER  = "bolder";

    private string $fontFamily = "Manrope-medium";
    private int $size = 12;
    private string $style = self::FONT_STYLE_NORMAL;
    private string|int $weight = self::FONT_STYLE_NORMAL; // You can use number instead of const value


    /**
     * Create config
     *
     * @return array
     */
    public function create(): array
    {
        return [
            'family'    => $this->getFontFamily(),
            'size'      => $this->getSize(),
            'style'     => $this->getStyle(),
            'weight'    => $this->getWeight(),
        ];
    }

    /**
     * Set font family
     *
     * @param  string $fontFamily
     * @return self
     */
    public function setFontFamily(string $fontFamily): self
    {
        if (!empty($fontFamily))
            $this->fontFamily = $fontFamily;

        return $this;
    }

    /**
     * Get font family
     *
     * @return string
     */
    public function getFontFamily(): string
    {
        return $this->fontFamily;
    }

    /**
     * Set font family
     *
     * @param  int $size
     * @return self
     */
    public function setSize(int $size): self
    {
        if ($size > 0)
            $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Set font style
     *
     * @param  string $style Use const values (self::FONT_STYLE_NORMAL | self::FONT_STYLE_ITALIC | ...)
     * @return self
     */
    public function setStyle(string $style = self::FONT_STYLE_NORMAL): self
    {
        if (!empty($style))
            $this->style = $style;

        return $this;
    }

    /**
     * Get style
     *
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }

    /**
     * Set font weight
     *
     * @param  string|int $weight Use int value or const values (self::FONT_WEIGHT_NORMAL | self::FONT_WEIGHT_BOLD | ...)
     * @return self
     */
    public function setWeight(string|int $weight = self::FONT_WEIGHT_NORMAL): self
    {
        if (!empty($weight))
            $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string|int int value or const values (self::FONT_WEIGHT_NORMAL | self::FONT_WEIGHT_BOLD | ...)
     */
    public function getWeight(): string|int
    {
        return $this->weight;
    }
}
