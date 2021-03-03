<?php
require __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL);

if (!isset($_REQUEST['isin'])) {
    $return = ['error' => 'No input'];
} else {
    $isinReader = IsinReader::getInstance($_REQUEST['isin']);

    $return = [ 'data' => [] ];

    foreach ($isinReader->getCourses() as $course)
    {
        $date = $course->getDate();

        $return['data'][] = [
            'date' => $date->format('Y-m-d'),
            'value' => $course->getValue()
        ];

        // set start date to 1st January of lowest year
        if (!isset($return['startDate']) && $date->format('md') < '0105') {
            $return['startDate'] = $date->format('Y-m').'-01';
        }

        $return['endDate'] = $date->format('d.m.Y');
    }

    if (isset($date) && $date->format('md') < '1228') {
        // set end date to 31th December of highest year
        $last_year = intval($date->format('Y')) - 1;
        $return['endDate'] = $last_year.'-12-31';
    }
}

echo json_encode($return);