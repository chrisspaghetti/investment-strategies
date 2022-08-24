<?php


class StrategyMonthly28 implements StrategyInterface
{
    /**
     * invest at 28th of each month
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

            try {
                $certainDate = new DateTime($month->format('Y-m').'-28');
            } catch (Exception $e) {
                throw new CalculationException($e->getMessage());
            }

            $course = $isinReader->getCourseOfDay($certainDate, false);

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