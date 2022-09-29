<?php


class TimeRange implements TimeRangeInterface
{
    /**
     * @var DateTime
     */
    protected DateTime $start;

    /**
     * @var DateTime
     */
    protected DateTime $end;

    /**
     * @var DateTime[]
     */
    protected array $months = [];

    /**
     * TimeRange constructor.
     * @param DateTime $startDate
     * @param DateTime $endDate
     */
    public function __construct(DateTime $startDate, DateTime $endDate)
    {
        $this->start = clone $startDate;
        $this->end   = clone $endDate;

        $copyStartDate = clone $startDate;
        $copyEndDate   = clone $endDate;

        $start    = $copyStartDate->modify('first day of this month');
        // 'first day of next month' can behave wrong, see https://derickrethans.nl/obtaining-the-next-month-in-php.html
        $end      = $copyEndDate->modify('last day of this month')->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        $this->months = [];
        foreach ($period as $dt) {
            try {
                $this->months[] = new DateTime($dt->format("Y-m").'-01');
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }
    }

    /**
     * @return DateTime[]
     */
    public function getMonths(): array
    {
        return $this->months;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        return $this->start;
    }

    /**
     * @return DateTime
     */
    public function getEndDate(): DateTime
    {
        return $this->end;
    }
}