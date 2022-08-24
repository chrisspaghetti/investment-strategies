<?php


class StrategyCashOnly implements StrategyInterface
{

    /**
     * no investments, only cash
     *
     * @param PortfolioInterface $portfolio
     * @param IsinReaderInterface $isinReader
     * @param TimeRangeInterface $timeRange
     * @param ConfiguratorInterface $configurator
     * @throws CalculationException
     */
    public function applyToPortfolio(PortfolioInterface $portfolio,
                                     IsinReaderInterface $isinReader,
                                     TimeRangeInterface $timeRange,
                                     ConfiguratorInterface $configurator)
    {
        $monthlyRate = $configurator->getAmountPerMonth();

        foreach($timeRange->getMonths() as $month)
        {
            $portfolio->addCash($month, $monthlyRate);
            // no investments, only cash
        }
    }
}