<?php


class Portfolio
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
     * @var Investment[]
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
            'text' => '+'.$amount.' '.$this->currency
        ];
    }

    /**
     * @param String $isin
     * @param DateTime $date
     * @param float $price
     * @param float $brokerCommission
     * @return Investment
     */
    public function buyStock(String $isin, DateTime $date, float $price, float $brokerCommission)
    {
        // how much to buy?
        $count = round(floatval(($this->cash - $brokerCommission) / $price), 4);

        $investment = new Investment($isin, $date, $price, $count, $brokerCommission);
        $this->investments[] = $investment;

        if (!isset($this->stocks[$isin])) {
            $this->stocks[$isin] = 0;
        }

        $this->history[] = [
            'date' => $date->format('Y-m-d'),
            'text' =>
                    'Investment: '.$count.' @ '.$price.' '.$this->currency.'. '.
                    'Fees: '.$brokerCommission.' '.$this->currency.'.'.
                    ' [-'.$this->cash.' '.$this->currency.']'
        ];

        $this->stocks[$isin] += $count;
        $this->cash = 0;

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