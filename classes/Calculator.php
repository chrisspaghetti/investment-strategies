<?php


class Calculator
{
    /**
     * @var Configurator
     */
    protected $configurator;

    /**
     * @var IsinReader
     */
    protected $isinReader;

    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * @var DateTime[]
     */
    protected $months = [];

    /**
     * Konstruktur
     * @param Configurator $configurator
     * @throws CalculationException
     */
    public function __construct(Configurator $configurator)
    {
        $this->configurator = $configurator;

        $isin = $this->configurator->getIsin();
        $this->isinReader = IsinReader::getInstance($isin);

        $configStartDate = $this->configurator->getStartDate();
        $configEndDate = $this->configurator->getEndDate();
        $isinStartDate = $this->isinReader->getStartDate();
        $isinEndDate = $this->isinReader->getEndDate();

        if (empty($configStartDate) && empty($isinStartDate)) {
            throw new CalculationException('no start date found');
        } else if (empty($configEndDate) && empty($isinEndDate)) {
            throw new CalculationException('no end date found');
        }

        if ($configStartDate === null || $configStartDate < $isinStartDate) {
            $this->startDate = $isinStartDate;
        } else {
            $this->startDate = $configStartDate;
        }

        // set start date to 1st day of next month if its in same month as course data exist
        if ($this->startDate->format('Y-m') == $isinStartDate->format('Y-m')) {
            // 'first day of next month' behaves wrong, see https://derickrethans.nl/obtaining-the-next-month-in-php.html
            $this->startDate->modify('last day of this month')->modify('+1 day');
        }

        if ($configEndDate === null || $configEndDate > $isinEndDate) {
            $this->endDate = $isinEndDate;
        } else {
            $this->endDate = $configEndDate;
        }

        // set end date to last day of month if its too close to current course data
        if ($this->endDate->format('Y-m') == $isinEndDate->format('Y-m')) {
            $this->endDate->modify('first day of this month')->modify('-1 day');
        }

        $this->months = $this->getMonthInBetween($this->startDate, $this->endDate);
    }


    /**
     * @return CalculationResult
     * @throws CalculationException
     */
    public function calc()
    {
        $portfolioPeter = $this->calculatePeter();
        $portfolioAshley = $this->calculateAshley();
        $portfolioMatthew = $this->calculateMatthew();
        $portfolioRosie = $this->calculateRosie();
        //$portfolioDoris = $this->calculateDoris();
        $portfolioDenise = $this->calculateDenise();
        $portfolioQuintus = $this->calculateQuintus();
        $portfolioWhitney = $this->calculateWhitney();
        $portfolioLarry = $this->calculateLarry();

        $result = new CalculationResult($this->startDate, $this->endDate);
        $result->add($portfolioPeter);
        $result->add($portfolioAshley);
        $result->add($portfolioMatthew);
        $result->add($portfolioRosie);
        //$result->add($portfolioDoris);
        $result->add($portfolioDenise);
        $result->add($portfolioQuintus);
        $result->add($portfolioWhitney);
        $result->add($portfolioLarry);

        return $result;
    }

    /**
     * @return Portfolio
     * @throws CalculationException
     */
    protected function calculatePeter()
    {
        // Peter Perfect invests at lowest monthly close
        $portfolio = new Portfolio('Peter Perfect');

        $monthlyRate = $this->configurator->getAmountPerMonth();

        foreach($this->months as $month)
        {
            $course = $this->isinReader->getLowestCloseOfMonth($month);

            if ($course === null) {
                throw new CalculationException('Not able to retrieve lowest close of month for '.$month->format('Y-m'));
            }

            $portfolio->addCash($month, $monthlyRate);
            $portfolio->buyStock($this->isinReader->getIsin(),
                                 $course->getDate(),
                                 $course->getValue(),
                                 $this->configurator->getBrokerCommissionAnyDayOfMonth()
                                );
        }

        return $portfolio;
    }

    /**
     * @return Portfolio
     */
    protected function calculateAshley()
    {
        // Ashley Action invests at start of year
        $portfolio = new Portfolio('Ashley Action');

        $monthlyRate = $this->configurator->getAmountPerMonth();
        $firstMonth = $this->months[array_key_first($this->months)];
        $lastMonth = $this->months[array_key_last($this->months)];

        foreach($this->months as $month)
        {
            // in Year 1 Ashley does not have full amount available if start date is not in January
            if ($month->format('Y-m') == $firstMonth->format('Y-m')) {
                $months_in_year = 12 - intval($month->format('m')) + 1;
                // add cash for first year
                $cash = round($monthlyRate * $months_in_year, 2);
                $portfolio->addCash($month, $cash);
            }

            // invest at start of year
            if (intval($month->format('m')) == 1) {
                $course = $this->isinReader->getCourseOfDay($month);

                // add cash for each new year
                if ($month->format('Y-m') != $firstMonth->format('Y-m')) {
                    // how much cash to add for this year?
                    if($lastMonth->format('Y') > $month->format('Y')) {
                        // add full amount when it's a full year
                        $portfolio->addCash($month, $this->configurator->getAmountPerYear());
                    } else {
                        // calculate amount for the year based on remaining months
                        $months_in_year = intval($lastMonth->format('m'));
                        $cash = round($monthlyRate * $months_in_year, 2);
                        $portfolio->addCash($month, $cash);
                    }
                }

                // invest
                $portfolio->buyStock(
                                $this->isinReader->getIsin(),
                                $course->getDate(),
                                $course->getValue(),
                                $this->configurator->getBrokerCommissionFirstDayOfMonth()
                            );
            }
        }

        return $portfolio;
    }

    /**
     * @return Portfolio
     */
    protected function calculateMatthew()
    {
        // Matthew Monthly invests in 12 even chunks at start of each month
        $portfolio = new Portfolio('Matthew Monthly');

        $monthlyRate = $this->configurator->getAmountPerMonth();

        foreach ($this->months as $month)
        {
            $portfolio->addCash($month, $monthlyRate);

            $course = $this->isinReader->getCourseOfDay($month);

            // invest
            $portfolio->buyStock(
                $this->isinReader->getIsin(),
                $course->getDate(),
                $course->getValue(),
                $this->configurator->getBrokerCommissionFirstDayOfMonth()
            );
        }

        return $portfolio;
    }

    /**
     * @return Portfolio
     */
    protected function calculateRosie()
    {
        // Rosie Rotten invests at highest monthly close
        $portfolio = new Portfolio('Rosie Rotten');

        $monthlyRate = $this->configurator->getAmountPerMonth();

        foreach ($this->months as $month)
        {
            $portfolio->addCash($month, $monthlyRate);

            $course = $this->isinReader->getHighestCloseOfMonth($month);

            // invest
            $portfolio->buyStock(
                $this->isinReader->getIsin(),
                $course->getDate(),
                $course->getValue(),
                $this->configurator->getBrokerCommissionAnyDayOfMonth()
            );
        }

        return $portfolio;
    }

    /**
     * @return Portfolio
     * @throws CalculationException
     */
    protected function calculateDoris()
    {
        // Doris Delay invests at 28th of each month
        $portfolio = new Portfolio('Doris Delay');

        $monthlyRate = $this->configurator->getAmountPerMonth();

        foreach ($this->months as $month)
        {
            $portfolio->addCash($month, $monthlyRate);

            try {
                $certainDate = new DateTime($month->format('Y-m').'-28');
            } catch (Exception $e) {
                throw new CalculationException($e->getMessage());
            }

            $course = $this->isinReader->getCourseOfDay($certainDate, false);

            // invest
            $portfolio->buyStock(
                $this->isinReader->getIsin(),
                $course->getDate(),
                $course->getValue(),
                $this->configurator->getBrokerCommissionAnyDayOfMonth()
            );
        }

        return $portfolio;
    }

    /**
     * @return Portfolio
     */
    protected function calculateDenise()
    {
        // Denise Delay invests on 1st day of every second month
        $portfolio = new Portfolio('Denise Delay');

        $monthlyRate = $this->configurator->getAmountPerMonth();

        foreach ($this->months as $month)
        {
            $portfolio->addCash($month, $monthlyRate);

            $month_number = intval($month->format('m'));
            if ($month_number % 2 == 1) {
                $course = $this->isinReader->getCourseOfDay($month);

                // invest
                $portfolio->buyStock(
                    $this->isinReader->getIsin(),
                    $course->getDate(),
                    $course->getValue(),
                    $this->configurator->getBrokerCommissionFirstDayOfMonth()
                );
            }
        }

        return $portfolio;
    }

    /**
     * @return Portfolio
     */
    protected function calculateQuintus()
    {
        // Quintus Quantus invests at start of January/April/July/October
        $portfolio = new Portfolio('Quintus Quantus');

        $monthlyRate = $this->configurator->getAmountPerMonth();

        foreach ($this->months as $month)
        {
            $portfolio->addCash($month, $monthlyRate);

            if (in_array(intval($month->format('m')), array(1,4,7,10))) {
                $course = $this->isinReader->getCourseOfDay($month);

                // invest
                $portfolio->buyStock(
                    $this->isinReader->getIsin(),
                    $course->getDate(),
                    $course->getValue(),
                    $this->configurator->getBrokerCommissionFirstDayOfMonth()
                );
            }
        }

        return $portfolio;
    }

    /**
     * This compare-function is used for sorting courses ascending (from low to high)
     *
     * @param Course $a
     * @param Course $b
     * @return int
     */
    public static function cmp_course(Course $a, Course $b)
    {
        $courseA = $a->getValue();
        $courseB = $b->getValue();

        if ($courseA == $courseB) {
            return 0;
        }

        return ($courseA > $courseB) ? 1 : -1;
    }

    /**
     * @return Portfolio
     */
    protected function calculateWhitney()
    {
        // Whitney Waiting invests 3 times: at all time lows, but only once per 3 years
        // Quintus Quantus invests at start of January/April/July/October
        $portfolio = new Portfolio('Whitney Waiting');

        // get lowest close per month
        $monthCourses = [];
        foreach ($this->months as $month)
        {
            $monthCourses[] = $this->isinReader->getLowestCloseOfMonth($month);
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
        $monthlyRate = $this->configurator->getAmountPerMonth();

        foreach ($this->months as $month)
        {
            $portfolio->addCash($month, $monthlyRate);

            // is this a relevant month for investing?
            foreach ($relevantCourses as $relevantCourse) {
                if ($relevantCourse->getDate()->format('Y-m') == $month->format('Y-m')) {
                    // invest
                    $portfolio->buyStock(
                        $this->isinReader->getIsin(),
                        $relevantCourse->getDate(),
                        $relevantCourse->getValue(),
                        $this->configurator->getBrokerCommissionAnyDayOfMonth()
                    );

                    // investment was done for this month
                    break;
                }
            }
        }

        return $portfolio;
    }

    /**
     * @return Portfolio
     */
    protected function calculateLarry()
    {
        // Larry Linger left his money in cash investments
        $portfolio = new Portfolio('Larry Linger');

        $monthlyRate = $this->configurator->getAmountPerMonth();

        foreach($this->months as $month)
        {
            $portfolio->addCash($month, $monthlyRate);
            // no investments, only cash
        }

        return $portfolio;
    }


    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return DateTime[]
     */
    protected function getMonthInBetween(DateTime $startDate, DateTime $endDate)
    {
        $copyStartDate = clone $startDate;
        $copyEndDate   = clone $endDate;

        $start    = $copyStartDate->modify('first day of this month');
        $end      = $copyEndDate->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        $result = [];
        foreach ($period as $dt) {
            try {
                $result[] = new DateTime($dt->format("Y-m").'-01');
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        return $result;
    }
}