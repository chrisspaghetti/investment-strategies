<?php


class Investment implements InvestmentInterface
{
    /**
     * @var DateTime
     */
    protected DateTime $date;

    /**
     * @var String
     */
    protected string $isin;

    /**
     * @var float
     */
    protected float $price;

    /**
     * @var float
     */
    protected float $count;

    /**
     * @var float
     */
    protected float $brokerCommission;

    /**
     * investment constructor
     * @param String $isin
     * @param DateTime $date
     * @param float $pricePerStock
     * @param float $count
     * @param float $brokerCommission
     */
    public function __construct(String $isin, DateTime $date, float $pricePerStock, float $count, float $brokerCommission)
    {
        $this->isin = $isin;
        $this->date = $date;
        $this->price = $pricePerStock;
        $this->count = $count;
        $this->brokerCommission = $brokerCommission;
    }

    /**
     * @param bool $with_commission
     * @return float
     */
    public function getPricePaid(bool $with_commission = true): float
    {
        $paid = floatval($this->price * $this->count);

        if ($with_commission) {
            $paid += $this->brokerCommission;
        }

        return $paid;
    }
}