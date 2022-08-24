<?php


class StrategyMonthlyHighest implements StrategyInterface
{

    /**
     * invest at highest monthly close
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

        foreach ($timeRange->getMonths() as $month)
        {
            $portfolio->addCash($month, $monthlyRate);

            $course = $isinReader->getHighestCloseOfMonth($month);

            // invest
            $portfolio->buyStock(
                $isinReader->getIsin(),
                $course->getDate(),
                $course->getValue(),
                $configurator->getBrokerCommissionAnyDayOfMonth()
            );
        }
    }
}