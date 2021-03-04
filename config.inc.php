<?php
define('IMPORT_DIR', dirname(__FILE__).'/import');

define('AVAILABLE_ISIN', serialize([
    'CH0007292359' => 'MSCI AC WORLD Index',
    'US78378X1072' => 'S&P 500 Index',
    'EU0009658202' => 'STOXX EUROPE 600 Index',
    'JP9010C00002' => 'Nikkei © Index',
    'IE00B4L5Y983' => 'iShares Core MSCI World (Acc)',
    'IE00B4K48X80' => 'iShares Core MSCI Europe (Acc)',
    'IE00BKM4GZ66' => 'iShares Core MSCI Emerging Markets IMI UCITS ETF (Acc)',
    'IE00B5BMR087' => 'iShares Core S&P 500 UCITS ETF (Acc)',
    //'IE00BYX2JD69' => 'iShares MSCI World SRI UCITS ETF EUR (Acc)'
    'DE0005933931' => 'iShares Core DAX UCITS ETF (Acc)',
    'IE00BYVJRR92' => 'iShares MSCI USA SRI UCITS ETF (Acc)',
    'LU0380865021' => 'Xtrackers EURO STOXX 50 UCITS ETF 1C (Acc)'
]));

