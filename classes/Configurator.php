<?php


class Configurator implements ConfiguratorInterface
{
    /**
     * @var String
     */
    protected $isin;

    /**
     * @var float
     */
    protected $amountPerMonth;

    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * @var float
     */
    protected $brokerCommissionFirstDayOfMonth;

    /**
     * @var float
     */
    protected $brokerCommissionAnyDayOfMonth;

    public function __construct()
    {
    }

    /**
     * @param String $value
     */
    public function setIsin(String $value)
    {
        $this->isin = $value;
    }

    /**
     * @param float $value
     */
    public function setAmountPerMonth(float $value)
    {
        $this->amountPerMonth = $value;
    }

    /**
     * @param float $value
     */
    public function setBrokerCommissionFirstDayOfMonth(float $value)
    {
        $this->brokerCommissionFirstDayOfMonth = $value;
    }

    /**
     * @param float $value
     */
    public function setBrokerCommissionAnyDayOfMonth(float $value)
    {
        $this->brokerCommissionAnyDayOfMonth = $value;
    }

    /**
     * @param DateTime $value
     */
    public function setStartDate(DateTime $value)
    {
        $this->startDate = $value;
    }

    /**
     * @param DateTime $value
     */
    public function setEndDate(DateTime $value)
    {
        $this->endDate = $value;
    }

    //  -------------------- GETTER -----------------------

    /**
     * @return String
     */
    public function getIsin()
    {
        return $this->isin;
    }

    /**
     * @return float
     */
    public function getBrokerCommissionFirstDayOfMonth()
    {
        return $this->brokerCommissionFirstDayOfMonth;
    }

    /**
     * @return float
     */
    public function getBrokerCommissionAnyDayOfMonth()
    {
        return $this->brokerCommissionAnyDayOfMonth;
    }

    /**
     * @return float
     */
    public function getAmountPerMonth()
    {
        return $this->amountPerMonth;
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
}