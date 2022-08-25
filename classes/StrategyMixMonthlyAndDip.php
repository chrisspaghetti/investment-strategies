<?php


class StrategyMixMonthlyAndDip implements StrategyInterface
{

    /**
     * @param PortfolioInterface $portfolio
     * @param IsinReaderInterface $isinReader
     * @param TimeRangeInterface $timeRange
     * @param ConfiguratorInterface $configurator
     */
    public function applyToPortfolio(PortfolioInterface $portfolio,
                                     IsinReaderInterface $isinReader,
                                     TimeRangeInterface $timeRange,
                                     ConfiguratorInterface $configurator)
    {
        $monthlyAmount = $configurator->getAmountPerMonth();
        $monthlyInvest = round(0.8 * $monthlyAmount, 2);

        foreach ($timeRange->getMonths() as $month) {
            $portfolio->addCash($month, $monthlyAmount);

            // invest 80% at 1st of each month
            $course = $isinReader->getCourseOfDay($month);
            $portfolio->buyStock(
                $isinReader->getIsin(),
                $course->getDate(),
                $course->getValue(),
                $configurator->getBrokerCommissionAnyDayOfMonth(),
                $monthlyInvest
            );

            // invest remaining cash after dip
            if (!isset($fromDate)) {
                $fromDate = clone $month;
            }
            $toDate = clone $month;
            $toDate->modify('last day of this month');

            unset($course);
            $course = $isinReader->getCourseAfterDrop($fromDate, $toDate, 10, 10);

            if ($course !== null && $course->getDate()->format('Y-m') == $month->format('Y-m')) {
                // use all remaining cash for buying the dip
                $portfolio->buyStock(
                    $isinReader->getIsin(),
                    $course->getDate(),
                    $course->getValue(),
                    $configurator->getBrokerCommissionAnyDayOfMonth()
                );

                unset($fromDate);
            }
            /*
            // for performance reasons: set $fromDate not too far away in the past
            if (isset($fromDate)) {
                $interval = date_diff($fromDate, $month);
                if (intval($interval->format('m')) >= 2) {
                    unset($fromDate);
                    $fromDate = clone $month;
                    $fromDate->modify('-1 month');
                }
            }*/
        }
    }
}