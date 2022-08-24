<?php

interface CalculationResultInterface
{
    /**
     * add Portfolio to the result
     *
     * @param PortfolioInterface $portfolio
     */
    public function add(PortfolioInterface $portfolio);

    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
     */
    public function getEndDate();

    /**
     * @return PortfolioInterface[]
     */
    public function getPortfolios();
}