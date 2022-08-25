<?php


class Portfolio implements PortfolioInterface
{
    /**
     * @var String
     */
    protected $name;

    /**
     * @var float
     */
    protected $cash;

    /**
     * @var String
     */
    protected $currency;

    /**
     * @var array
     */
    protected $stocks = []; // isin => count as float

    /**
     * @var InvestmentInterface[]
     */
    protected $investments = [];

    /**
     * @var array
     */
    protected $history = [];

    /**
     * Konstruktur
     * @param String $name
     * @param String $currency
     */
    public function __construct(String $name, String $currency = '€')
    {
        $this->name = $name;
        $this->cash = 0;
        $this->currency = $currency;
        $this->stocks = [];
        $this->history = [];
    }

    /**
     * @param DateTime $date
     * @param float $amount
     */
    public function addCash(DateTime $date, float $amount)
    {
        $this->cash += $amount;

        $this->history[] = [
            'date' => $date->format('Y-m-d'),
            'text' => '+'.$amount.' '.$this->currency.' ['.$this->cash.' '.$this->currency.']'
        ];
    }

    /**
     * Buy some stocks on a certain date for the given price
     *
     * @param String $isin                  ISIN of stock which is bought
     * @param DateTime $date                purchase date
     * @param float $price                  stock price
     * @param float $brokerCommission       fee amount for the broker that is substracted from the available cash
     * @param float $useSpecificCashAmount  if "0" then use all cash that is in the portfolio
     * @return InvestmentInterface
     */
    public function buyStock(String $isin, DateTime $date, float $price, float $brokerCommission, float $useSpecificCashAmount = 0)
    {
        // how much to buy?
        if ($useSpecificCashAmount > 0) {
            $amountSpend = $useSpecificCashAmount;
            $amountForPurchase = $useSpecificCashAmount - $brokerCommission;
        } else {
            $amountSpend = $this->cash;
            $amountForPurchase = $this->cash - $brokerCommission;
        }

        if ($amountForPurchase < 0) {
            $amountForPurchase = 0;
        }

        $count = round(floatval($amountForPurchase / $price), 4);

        $investment = new Investment($isin, $date, $price, $count, $brokerCommission);
        $this->investments[] = $investment;

        if (!isset($this->stocks[$isin])) {
            $this->stocks[$isin] = 0;
        }

        $this->history[] = [
            'date' => $date->format('Y-m-d'),
            'text' =>
                    'Buy: '.$count.' @ '.$price.' '.$this->currency.'. '.
                    'Fee: '.$brokerCommission.' '.$this->currency.'.'.
                    ' -'.$amountSpend.' '.$this->currency.'.'
        ];

        $this->stocks[$isin] += $count;
        $this->cash -= $amountSpend;

        return $investment;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array with ['date' => Y-m-d, 'text' => String]
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param DateTime $date
     * @return float|int
     */
    public function getTotalValue(DateTime $date)
    {
        $return = $this->cash;

        foreach ($this->stocks as $isin => $count)
        {
            $isinReader = IsinReader::getInstance($isin);
            $course = $isinReader->getCourseOfDay($date);

            $return += round(floatval($course->getValue() * $count), 4);
        }

        return $return;
    }

    /**
     * @return float
     */
    public function getInvestedCash()
    {
        $return = 0;

        foreach ($this->investments as $investment) {
            $return += $investment->getPricePaid();
        }

        return $return;
    }
}