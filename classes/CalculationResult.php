<?php


class CalculationResult implements CalculationResultInterface
{
    /**
     * @var PortfolioInterface[]
     */
    protected array $portfolios = [];

    /**
     * @var DateTime
     */
    protected DateTime $startDate;

    /**
     * @var DateTime
     */
    protected DateTime $endDate;

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
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    /**
     * @return PortfolioInterface[]
     */
    public function getPortfolios(): array
    {
        return $this->portfolios;
    }
}