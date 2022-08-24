<?php


class StrategyAllTimeLowsAfter3Years implements StrategyInterface
{

    /**
     * invest 3 times: at all time lows, but only once per 3 years
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
        // get lowest close per month
        $monthCourses = [];
        foreach ($timeRange->getMonths() as $month)
        {
            $monthCourses[] = $isinReader->getLowestCloseOfMonth($month);
        }

        // sort by values ascending
        usort($monthCourses, array("Calculator", "cmp_course"));

        // get the 3 "perfect dates"
        $relevantCourses = [];
        foreach ($monthCourses as $course) {
            if (count($relevantCourses) >= 3) {
                break;
            }

            // rule "once per 3 years" fulfilled?
            $year = intval($course->getDate()->format('Y'));
            $ok = true;

            foreach ($relevantCourses as $relevantCourse) {
                $year2 = intval($relevantCourse->getDate()->format('Y'));
                $diff = abs($year - $year2);
                if ($diff < 3) {
                    $ok = false;
                    break;
                }
            }

            if ($ok) {
                $relevantCourses[] = $course;
            }
        }

        // add cash and do the investments
        $monthlyRate = $configurator->getAmountPerMonth();

        foreach ($timeRange->getMonths() as $month)
        {
            $portfolio->addCash($month, $monthlyRate);

            // is this a relevant month for investing?
            foreach ($relevantCourses as $relevantCourse) {
                if ($relevantCourse->getDate()->format('Y-m') == $month->format('Y-m')) {
                    // invest
                    $portfolio->buyStock(
                        $isinReader->getIsin(),
                        $relevantCourse->getDate(),
                        $relevantCourse->getValue(),
                        $configurator->getBrokerCommissionAnyDayOfMonth()
                    );

                    // investment was done for this month
                    break;
                }
            }
        }
    }
}