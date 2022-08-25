<?php


class Calculator implements CalculatorInterface
{
    /**
     * @var ConfiguratorInterface
     */
    protected $configurator;

    /**
     * @var IsinReaderInterface
     */
    protected $isinReader;

    /**
     * @var TimeRangeInterface
     */
    protected $timeRange;

    /**
     * This compare-function is used for sorting courses ascending (from low to high)
     *
     * @param CourseInterface $a
     * @param CourseInterface $b
     * @return int
     */
    public static function cmp_course(CourseInterface $a, CourseInterface $b)
    {
        $courseA = $a->getValue();
        $courseB = $b->getValue();

        if ($courseA == $courseB) {
            return 0;
        }

        return ($courseA > $courseB) ? 1 : -1;
    }

    /**
     * constructor
     * @param ConfiguratorInterface $configurator
     * @throws CalculationException
     */
    public function __construct(ConfiguratorInterface $configurator)
    {
        $this->configurator = $configurator;
        $this->isinReader = IsinReader::getInstance($this->configurator->getIsin());

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
            $startDate = $isinStartDate;
        } else {
            $startDate = $configStartDate;
        }

        // set start date to 1st day of next month if its in same month as course data exist
        if ($startDate->format('Y-m') == $isinStartDate->format('Y-m') && intval($isinStartDate->format('d')) > 5) {
            // 'first day of next month' can behave wrong, see https://derickrethans.nl/obtaining-the-next-month-in-php.html
            $startDate->modify('last day of this month')->modify('+1 day');
        }

        if ($configEndDate === null || $configEndDate > $isinEndDate) {
            $endDate = $isinEndDate;
        } else {
            $endDate = $configEndDate;
        }

        // set end date to last day of last month if the current course data is not already end of month
        if ($endDate->format('Y-m') == $isinEndDate->format('Y-m') && intval($isinEndDate->format('d')) < 26) {
            $endDate->modify('first day of this month')->modify('-1 day');
        }

        $this->timeRange = new TimeRange($startDate, $endDate);
    }

    /**
     * @return CalculationResultInterface
     * @throws CalculationException
     */
    public function calc()
    {
        $result = new CalculationResult($this->timeRange->getStartDate(), $this->timeRange->getEndDate());

        $participants = unserialize(PARTICIPANTS);
        foreach ($participants as $participant_name => $participant) {
            $portfolio = new Portfolio($participant_name);

            if (0 == 1) { // for where-used list
                new StrategyAllTimeLowsAfter3Years();
                new StrategyCashOnly();
                new StrategyHalfyearlyBuyDip();
                new StrategyMonthly28();
                new StrategyMonthlyHighest();
                new StrategyMonthlyLowest();
                new StrategyQuarterly();
                new StrategyTwoMonthly();
                new StrategyYearly();
                new StrategyMixMonthlyAndDip();

                $strategy = new StrategyMonthlyFirst();
                $strategy->applyToPortfolio($portfolio, $this->isinReader, $this->timeRange, $this->configurator);
            }

            // dynamic class instantiation
            $strategy = new $participant[1]();
            $strategy->applyToPortfolio($portfolio, $this->isinReader, $this->timeRange, $this->configurator);
            $result->add($portfolio);
        }

        return $result;
    }

}