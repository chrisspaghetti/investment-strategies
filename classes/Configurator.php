<?php


class Configurator implements ConfiguratorInterface
{
    /**
     * @var String
     */
    protected string $isin;

    /**
     * @var float
     */
    protected float $amountPerMonth;

    /**
     * @var DateTime
     */
    protected DateTime $startDate;

    /**
     * @var DateTime
     */
    protected DateTime $endDate;

    /**
     * @var float
     */
    protected float $brokerCommissionFirstDayOfMonth;

    /**
     * @var float
     */
    protected float $brokerCommissionAnyDayOfMonth;

    public function __construct()
    {
    }

    /**
     * @param String $value
     * @return void
     */
    public function setIsin(String $value): void
    {
        $this->isin = $value;
    }

    /**
     * @param float $value
     * @return void
     */
    public function setAmountPerMonth(float $value): void
    {
        $this->amountPerMonth = $value;
    }

    /**
     * @param float $value
     * @return void
     */
    public function setBrokerCommissionFirstDayOfMonth(float $value): void
    {
        $this->brokerCommissionFirstDayOfMonth = $value;
    }

    /**
     * @param float $value
     * @return void
     */
    public function setBrokerCommissionAnyDayOfMonth(float $value): void
    {
        $this->brokerCommissionAnyDayOfMonth = $value;
    }

    /**
     * @param DateTime $value
     * @return void
     */
    public function setStartDate(DateTime $value): void
    {
        $this->startDate = $value;
    }

    /**
     * @param DateTime $value
     * @return void
     */
    public function setEndDate(DateTime $value): void
    {
        $this->endDate = $value;
    }

    //  -------------------- GETTER -----------------------

    /**
     * @return String
     */
    public function getIsin(): string
    {
        return $this->isin;
    }

    /**
     * @return float
     */
    public function getBrokerCommissionFirstDayOfMonth(): float
    {
        return $this->brokerCommissionFirstDayOfMonth;
    }

    /**
     * @return float
     */
    public function getBrokerCommissionAnyDayOfMonth(): float
    {
        return $this->brokerCommissionAnyDayOfMonth;
    }

    /**
     * @return float
     */
    public function getAmountPerMonth(): float
    {
        return $this->amountPerMonth;
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
}