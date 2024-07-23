<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;


/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class GradientColorConfig
{

    private int $startPointX;
    private int $startPointY;
    private int $endPointX;
    private int $endPointY;
    private string $startColor;
    private string $endColor;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults

        $this->setStartPoint(5, 0);
        $this->setEndPoint(5, 100);

        $this->setColorStart('rgba(251, 150, 0, 0.18)');
        $this->setColorEnd('rgba(251, 150, 0, 0.02)');
    }

    /**
     * Create config
     *
     * @return array
     */
    public function create(): array
    {
        return [
            'startPointX'   => $this->getStartPointX(),
            'startPointY'   => $this->getStartPointY(),
            'endPointX'     => $this->getEndPointX(),
            'endPointY'     => $this->getEndPointY(),
            'startColor'    => $this->getColorStart(),
            'endColor'      => $this->getColorEnd(),
        ];
    }

    /**
     * Set start point
     *
     * @param  int $startPointX
     * @param  int $startPointY
     * @return self
     */
    public function setStartPoint(int $startPointX, int $startPointY): self
    {
        $this->setStartPointX($startPointX);
        $this->setStartPointY($startPointY);
        return $this;
    }

    /**
     * Get start point
     *
     * @return array [X,Y]
     */
    public function getStartPoint(): array
    {
        return [$this->getStartPointX(), $this->getStartPointY()];
    }

    /**
     * Set start point X
     *
     * @param int $startPointX
     * @return self
     */
    public function setStartPointX(int $startPointX): self
    {
        $this->startPointX = $startPointX;
        return $this;
    }

    /**
     * Get start point X
     *
     * @return int
     */
    public function getStartPointX(): int
    {
        return $this->startPointX;
    }

    /**
     * Set start point Y
     *
     * @param int $startPointY
     * @return self
     */
    public function setStartPointY(int $startPointY): self
    {
        $this->startPointY = $startPointY;
        return $this;
    }

    /**
     * Get start point Y
     *
     * @return int
     */
    public function getStartPointY(): int
    {
        return $this->startPointY;
    }

    /**
     * Set end point
     *
     * @param  int $endPointX
     * @param  int $endPointY
     * @return self
     */
    public function setEndPoint(int $endPointX, int $endPointY): self
    {
        $this->setEndPointX($endPointX);
        $this->setEndPointY($endPointY);
        return $this;
    }

    /**
     * Get end point
     *
     * @return array [X,Y]
     */
    public function getEndPoint(): array
    {
        return [$this->getEndPointX(), $this->getEndPointY()];
    }

    /**
     * Set end point X
     *
     * @param int $endPointX
     * @return self
     */
    public function setEndPointX(int $endPointX): self
    {
        $this->endPointX = $endPointX;
        return $this;
    }

    /**
     * Get end point X
     *
     * @return int
     */
    public function getEndPointX(): int
    {
        return $this->endPointX;
    }

    /**
     * Set end point Y
     *
     * @param int $endPointY
     * @return self
     */
    public function setEndPointY(int $endPointY): self
    {
        $this->endPointY = $endPointY;
        return $this;
    }

    /**
     * Get end point Y
     *
     * @return int
     */
    public function getEndPointY(): int
    {
        return $this->endPointY;
    }

    /**
     * Set color
     *
     * @param  string $startColor
     * @param  string $endColor
     * @return self
     */
    public function setColor(string $startColor, string $endColor): self
    {
        $this->setColorStart($startColor);
        $this->setColorEnd($endColor);

        return $this;
    }

    /**
     * Get color
     *
     * @return array [startColor, endColor]
     */
    public function getColor(): array
    {
        return [$this->getColorStart(), $this->getColorEnd()];
    }

    /**
     * Set start color
     *
     * @param  string $startColor
     * @return self
     */
    public function setColorStart(string $startColor): self
    {
        $this->startColor = $startColor;
        return $this;
    }

    /**
     * Get start color
     *
     * @return string
     */
    public function getColorStart(): string
    {
        return $this->startColor;
    }

    /**
     * Set end color
     *
     * @param  string $endColor
     * @return self
     */
    public function setColorEnd(string $endColor): self
    {
        $this->endColor = $endColor;
        return $this;
    }

    /**
     * Get end color
     *
     * @return string
     */
    public function getColorEnd(): string
    {
        return $this->endColor;
    }
}
