<?php


class StrategyMonthlyLowest implements StrategyInterface
{

    /**
     * invest at lowest monthly close
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
            $course = $isinReader->getLowestCloseOfMonth($month);

            if ($course === null) {
                throw new CalculationException('Not able to retrieve lowest close of month for '.$month->format('Y-m'));
            }

            $portfolio->addCash($month, $monthlyRate);
            $portfolio->buyStock($isinReader->getIsin(),
                $course->getDate(),
                $course->getValue(),
                $configurator->getBrokerCommissionAnyDayOfMonth()
            );
        }
    }
}