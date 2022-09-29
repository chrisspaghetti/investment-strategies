<?php


class StrategyHalfyearlyBuyDip implements StrategyInterface
{
    /**
     * invest once per half year as soon as the course dropped by 10% within 10 days
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

        $months = $timeRange->getMonths();
        $firstMonth = $months[array_key_first($months)];

        try {
            foreach ($months as $month) {
                $portfolio->addCash($month, $monthlyRate);

                if (intval($month->format('m')) == 1
                    || ($firstMonth->format('m') < 7 && $firstMonth->format('Y-m') == $month->format('Y-m')) ) {
                    $fromDate = $month;
                    $toDate = new DateTime($month->format('Y') . '-06-30');

                    $course = $isinReader->getCourseAfterDrop($fromDate, $toDate, 10, 10);
                } else if (intval($month->format('m')) == 7
                    || ($firstMonth->format('m') >= 7 && $firstMonth->format('Y-m') == $month->format('Y-m'))) {
                    $fromDate = $month;
                    $toDate = new DateTime($month->format('Y') . '-12-31');

                    $course = $isinReader->getCourseAfterDrop($fromDate, $toDate, 10, 10);
                }

                if (isset($course) && ($course->getDate()->format('Y-m') == $month->format('Y-m'))) {
                    // invest
                    $portfolio->buyStock(
                        $isinReader->getIsin(),
                        $course->getDate(),
                        $course->getValue(),
                        $configurator->getBrokerCommissionAnyDayOfMonth()
                    );
                }
            }
        } catch (Exception $e) {
            throw new CalculationException($e->getMessage());
        }
    }
}