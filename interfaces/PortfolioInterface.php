<?php

interface PortfolioInterface
{
    /**
     * @param String $isin
     * @param DateTime $date
     * @param float $price
     * @param float $brokerCommission
     * @return InvestmentInterface
     */
    public function buyStock(String $isin, DateTime $date, float $price, float $brokerCommission);

    /**
     * @return String
     */
    public function getName();

    /**
     * @param DateTime $date
     * @return float|int
     */
    public function getTotalValue(DateTime $date);

}