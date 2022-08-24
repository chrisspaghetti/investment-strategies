<?php


class Investment implements InvestmentInterface
{
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var String
     */
    protected $isin;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $count;

    /**
     * @var float
     */
    protected $brokerCommission;

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
    public function getPricePaid(bool $with_commission = true)
    {
        $paid = floatval($this->price * $this->count);

        if ($with_commission) {
            $paid += $this->brokerCommission;
        }

        return $paid;
    }
}