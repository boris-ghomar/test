<?php

namespace App\HHH_Library\Charts\ChartJs\Configs\Partials;


/**
 * Based on:
 * https://www.chartjs.org/docs/latest/getting-started/
 */


class DataConfig
{

    private array $labels = [];
    private array $datasets = [];

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        // Defaults
    }

    /**
     * Create config
     *
     * @return array
     */
    public function create(): array
    {
        return [
            'labels'    => $this->getLabels(),
            'datasets'   => $this->getDataSets(),
        ];
    }

    /**
     * Set labels
     *
     * @param  array $labels
     * @return self
     */
    public function setLabels(array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Get labels
     *
     * @return array
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Add data set
     *
     * @param  \App\HHH_Library\Charts\ChartJs\Configs\Partials\DataSetConfig $dataSetConfig
     * @return self
     */
    public function addDataSet(string $chartHtmlContainerId, DataSetConfig $dataSetConfig): self
    {
        array_push($this->datasets, $dataSetConfig->create($chartHtmlContainerId));
        return $this;
    }

    /**
     * Get data sets
     *
     * @return array
     */
    public function getDataSets(): array
    {
        return $this->datasets;
    }
}
