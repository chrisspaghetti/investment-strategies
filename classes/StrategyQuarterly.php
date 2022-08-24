<?php


class StrategyQuarterly implements StrategyInterface
{

    /**
     * invest at start of January/April/July/October
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

            if (in_array(intval($month->format('m')), array(1,4,7,10))) {
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