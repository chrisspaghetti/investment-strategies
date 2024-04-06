<?php
require __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL);

if (!isset($_REQUEST['isin'])) {
    $return = ['error' => 'No input'];
} else {
    $isinReader = IsinReader::getInstance($_REQUEST['isin']);

    $return = [ 'currency' => $isinReader->getCurrency(),
                'data' => [] ];

    foreach ($isinReader->getCourses() as $course)
    {
        $date = $course->getDate();

        $return['data'][] = [
            'date' => $date->format('Y-m-d'),
            'value' => $course->getValue()
        ];

        // set min value
        if (!isset($return['min']) || $return['min']  > $course->getValue()) {
            $return['min'] = $course->getValue();
        }

        // set max value
        if (!isset($return['max']) || $return['max'] < $course->getValue()) {
            $return['max'] = $course->getValue();
        }

        // set start date to 1st January of the lowest year
        if (!isset($return['startDate']) && $date->format('md') < '0105') {
            $return['startDate'] = $date->format('Y-m').'-01';
        }

        $return['endDate'] = $date->format('Y-m-d');
        if ($date->format('md')  < '1228') {
            // set end date to 31st December of the highest year
            $last_year = intval($date->format('Y')) - 1;
            $return['endDate'] = $last_year.'-12-31';
        }
    }
}

echo json_encode($return);