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
    public function getStartDate(): DateTime;

    /**
     * @return DateTime
     */
    public function getEndDate(): DateTime;

    /**
     * @return PortfolioInterface[]
     */
    public function getPortfolios(): array;
}