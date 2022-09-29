<?php

interface PortfolioInterface
{
    /**
     * Buy some stocks on a certain date for the given price
     *
     * @param String $isin                  ISIN of stock which is bought
     * @param DateTime $date                purchase date
     * @param float $price                  stock price
     * @param float $brokerCommission       fee amount for the broker that is substracted from the available cash
     * @param float $useSpecificCashAmount  if "0" then use all cash that is in the portfolio
     * @return InvestmentInterface
     */
    public function buyStock(String $isin, DateTime $date, float $price, float $brokerCommission, float $useSpecificCashAmount = 0): InvestmentInterface;

    /**
     * @return String
     */
    public function getName(): string;

    /**
     * @param DateTime $date
     * @return float
     */
    public function getTotalValue(DateTime $date): float;
}