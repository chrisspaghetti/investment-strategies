<?php


class CalculationResult
{
    /**
     * @var Portfolio[]
     */
    protected $portfolios = [];

    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * Konstruktur
     * @param DateTime $startDate
     * @param DateTime $endDate
     */
    public function __construct(DateTime $startDate, DateTime $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @param Portfolio $portfolio
     */
    public function add(Portfolio $portfolio)
    {
        $this->portfolios[] = $portfolio;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return Portfolio[]
     */
    public function getPortfolios()
    {
        return $this->portfolios;
    }
}