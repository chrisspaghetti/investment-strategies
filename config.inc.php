<?php
define('IMPORT_DIR', dirname(__FILE__).'/import');

define('PARTICIPANTS', serialize([
    // name           => [0 => description for output, 1 => a PHP class that implements the StrategyInterface]
    'Matthew Monthly' => ['Matthew Monthly invests at start of each month.', 'StrategyMonthlyFirst'],
    'Peter Perfect'   => ['Peter Perfect invests at lowest monthly close.', 'StrategyMonthlyLowest'],
    'Rosie Rotten'    => ['Rosie Rotten invests at highest monthly close.', 'StrategyMonthlyHighest'],
    'Elisabeth Ello'  => ['Elisabeth Ello invests at 28th of each month', 'StrategyMonthly28'],
    'Ashley Action'   => ['Ashley Action invests at start of a year.', 'StrategyYearly'],
    'Sabrina Secondo' => ['Sabrina Secondo invests on 1st day of every second month', 'StrategyTwoMonthly'],
    'Quintus Quantus' => ['Quintus Quantus invests at start of January/April/July/October.', 'StrategyQuarterly'],
    'Denise Dip'      => ['Denise Dip invests 80% of her available amount per month at start of each month. 
                        Each time the course dropped by 10% within 10 days Denise invests her remaining cash.', 'StrategyMixMonthlyAndDip'],
    'Trisha Tippit'   => ['Trisha Tippit invests once per half year as soon as the course dropped by 10% within 10 days.', 'StrategyHalfyearlyBuyDip'],
    'Whitney Waiting' => ['Whitney Waiting invests up to 3 times at all time lows, but only once per 3 years.', 'StrategyAllTimeLowsAfter3Years'],
    'Larry Linger'    => ['Larry Linger leaves his money in cash investments.', 'StrategyCashOnly'],
]));

define('AVAILABLE_ISIN', serialize([
    // isin        => [0 => ETF or Index Name, 1 => currency of values]
    'MSCIWORLDIDX' => ['MSCI World Index', 'Pts'],
    'US78378X1072' => ['S&P 500 Index', 'Pts'],
    'EU0009658202' => ['STOXX EUROPE 600 Index', 'Pts'],
    'JP9010C00002' => ['Nikkei © Index', 'Pts'],
    'IE00B4L5Y983' => ['iShares Core MSCI World (Acc)', '€'],
    'IE00B4K48X80' => ['iShares Core MSCI Europe (Acc)', '€'],
    'IE00BKM4GZ66' => ['iShares Core MSCI Emerging Markets IMI UCITS ETF (Acc)', '€'],
    'IE00B5BMR087' => ['iShares Core S&P 500 UCITS ETF (Acc)', '€'],
    'DE0005933931' => ['iShares Core DAX UCITS ETF (Acc)', '€'],
    'IE00BYVJRR92' => ['iShares MSCI USA SRI UCITS ETF (Acc)', '€'],
    'LU0380865021' => ['Xtrackers EURO STOXX 50 UCITS ETF 1C (Acc)', '€']
]));
