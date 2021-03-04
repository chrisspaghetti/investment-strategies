<?php
require __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL);

if (!isset($_REQUEST['isin'])) {
    $return = ['error' => 'No input'];
} else {
    try {
        $configurator = new Configurator();
        $configurator->setIsin($_REQUEST['isin']);
        $configurator->setAmountPerMonth(intval($_REQUEST['amount']));
        $configurator->setBrokerCommissionAnyDayOfMonth(Helper::tofloat($_REQUEST['brokerCommissionAnyDayOfMonth']));
        $configurator->setBrokerCommissionFirstDayOfMonth(Helper::tofloat($_REQUEST['brokerCommissionFirstDayOfMonth']));

        if (!empty($_REQUEST['startDate'])) {
            $date = DateTime::createFromFormat('d.m.Y', $_REQUEST['startDate']);
            if ($date !== false) {
                $configurator->setStartDate($date);
            }
        }

        if (!empty($_REQUEST['endDate'])) {
            $date = DateTime::createFromFormat('d.m.Y', $_REQUEST['endDate']);
            if ($date !== false) {
                $configurator->setEndDate($date);
            }
        }

        $calculator = new Calculator($configurator);
        $calculationResult = $calculator->calc();

        $return = array(
            'startDate' => $calculationResult->getStartDate()->format('Y-m-d'),
            'endDate'   => $calculationResult->getEndDate()->format('Y-m-d'),
            'data'      => []
        );

        foreach ($calculationResult->getPortfolios() as $portfolio)
        {
            $portfolioDetails = [];
            $portfolioDetails['name'] = $portfolio->getName();
            $portfolioDetails['value'] = round($portfolio->getTotalValue($calculationResult->getEndDate()), 2);
            $portfolioDetails['history'] = [];

            foreach ($portfolio->getHistory() as $historyDetails)
            {
                $portfolioDetails['history'][] = [
                    'date' => $historyDetails['date'],
                    'text' => $historyDetails['text']
                ];
            }

            $return['data'][] = $portfolioDetails;
        }
    } catch (CalculationException $e) {
        $return = ['error' => $e->getMessage()];
    }
}

echo json_encode($return);