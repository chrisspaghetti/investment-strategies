<?php


interface InvestmentInterface
{
    /**
     * @param bool $with_commission
     * @return float
     */
    public function getPricePaid(bool $with_commission = true);
}