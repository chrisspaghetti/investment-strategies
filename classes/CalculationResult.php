<?php


class CalculationResult implements CalculationResultInterface
{
    /**
     * @var PortfolioInterface[]
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
     * @param PortfolioInterface $portfolio
     */
    public function add(PortfolioInterface $portfolio)
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
     * @return PortfolioInterface[]
     */
    public function getPortfolios()
    {
        return $this->portfolios;
    }
}