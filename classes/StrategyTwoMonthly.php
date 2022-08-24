<?php


class StrategyTwoMonthly implements StrategyInterface
{

    /**
     * invest on 1st day of every second month
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

            $month_number = intval($month->format('m'));
            if ($month_number % 2 == 1) {
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