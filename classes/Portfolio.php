<?php


class Portfolio implements PortfolioInterface
{
    /**
     * @var String
     */
    protected string $name;

    /**
     * @var float
     */
    protected float $cash;

    /**
     * @var String
     */
    protected string $currency;

    /**
     * @var array
     */
    protected array $stocks = []; // isin => count as float

    /**
     * @var InvestmentInterface[]
     */
    protected array $investments = [];

    /**
     * @var array
     */
    protected array $history = [];

    /**
     * Konstruktur
     * @param String $name
     * @param String $currency
     */
    public function __construct(string $name, string $currency = '€')
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
     * @return void
     */
    public function addCash(DateTime $date, float $amount): void
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
    public function buyStock(string $isin, DateTime $date, float $price, float $brokerCommission, float $useSpecificCashAmount = 0): InvestmentInterface
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

        $count = round(($amountForPurchase / $price), 4);

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array with ['date' => Y-m-d, 'text' => String]
     */
    public function getHistory(): array
    {
        return $this->history;
    }

    /**
     * @param DateTime $date
     * @return float
     */
    public function getTotalValue(DateTime $date): float
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
    public function getInvestedCash(): float
    {
        $return = 0.00;

        foreach ($this->investments as $investment) {
            $return += $investment->getPricePaid();
        }

        return $return;
    }
}