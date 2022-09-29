<?php


class StrategyYearly implements StrategyInterface
{
    /**
     * invest at start of each year
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

            // is it January?
            if (intval($month->format('m')) == 1) {
                $course = $isinReader->getCourseOfDay($month);

                // invest
                $portfolio->buyStock(
                    $isinReader->getIsin(),
                    $course->getDate(),
                    $course->getValue(),
                    $configurator->getBrokerCommissionFirstDayOfMonth()
                );
            }
        }
    }
}