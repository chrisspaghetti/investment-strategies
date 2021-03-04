<?php


class IsinReader
{
    /**
     * @var String
     */
    protected $isin;

    /**
     * @var Course[]
     */
    protected $courses = []; // Y-m-d => Course

    /**
     * @var IsinReader[]
     */
    private static $instances = []; // isin => IsinReader

    /**
     * Konstruktur
     * @param String $isin
     */
    protected function __construct(String $isin)
    {
        $this->isin = $isin;

        $this->readData();
    }

    /**
     * @param String $isin
     * @return IsinReader
     */
    public static function getInstance(String $isin)
    {
        if (!isset(self::$instances[$isin])) {
           self::$instances[$isin] = new IsinReader($isin);
        }

        return self::$instances[$isin];
    }

    /**
     * @return String
     */
    public function getIsin()
    {
        return $this->isin;
    }

    /**
     * @return DateTime|null
     */
    public function getStartDate()
    {
        if (empty($this->courses))
            return null;

        $date = array_key_first($this->courses);

        try {
            return new DateTime($date);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate()
    {
        if (empty($this->courses))
            return null;

        $date = array_key_last($this->courses);

        try {
            return new DateTime($date);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    protected function readData()
    {
        // the File is a downloaded CSV file from ariva.de e.g. https://www.ariva.de/ishares_msci_europe_ucits_etf_eur_acc-fonds/historische_kurse
        $file = IMPORT_DIR.'/'.$this->isin.'.csv';

        // read file
        $handle = fopen($file, "r");
        while (($line=fgets($handle)) !== false)
        {
            $parts = explode(';', $line);

            if (empty($parts[4])) {
                continue;
            }

            $date_string = trim($parts[0]);

            if (preg_match("/^([0-9]{4})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date_string, $matches) != false) {
                // check if valid date
                if (checkdate($matches[2], $matches[3], $matches[1]) === false) {
                    die('invalid date: ' . $date_string);
                }
                $date = $date_string; // Y-m-d
            } else if(preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.([0-9]{4})$/", $date_string, $matches) != false) {
                // check if valid date
                if (checkdate($matches[2], $matches[1], $matches[3]) === false) {
                    die('invalid date: ' . $date_string);
                }
                $date = $matches[3].'-'.$matches[2].'-'.$matches[1];
            } else {
                continue;
            }

            $courseValue = Helper::tofloat($parts[4]);

            try {
                $dateTime = new DateTime($date);
            } catch (Exception $e) {
                die($e->getMessage());
            }

            if ($courseValue > 0) {
                $this->courses[$date] = new Course($courseValue, $dateTime);
            }
        }

        // make sure courses are sorted by date
        ksort($this->courses);
    }

    /**
     * @param DateTime $firstOfMonth
     * @return Course|null
     */
    public function getLowestCloseOfMonth(DateTime $firstOfMonth)
    {
        $return = null;

        $date = clone $firstOfMonth;
        while ($date->format('Y-m') == $firstOfMonth->format('Y-m'))
        {
            if (isset($this->courses[$date->format('Y-m-d')])) {
                $course = $this->courses[$date->format('Y-m-d')];

                if ($return === null || $course->getValue() < $return->getValue()) {
                    $return = $course;
                }
            }

            $date->modify('+1 day');
        }

        return $return;
    }

    /**
     * @param int $year
     * @param int $halfyear
     * @return Course|null
     */
    public function getLowestCloseOfHalfyear(int $year, int $halfyear)
    {
        $return = null;

        try {
            $date = new DateTime($year.'-01-01');
        } catch (Exception $e) {
            die($e->getMessage());
        }

        while (intval($date->format('Y')) == $year)
        {
            if (isset($this->courses[$date->format('Y-m-d')])
                && (( $halfyear == 1 && in_array(intval($date->format('m')), array(1,2,3,4,5,6)))
                 || ( $halfyear == 2 && in_array(intval($date->format('m')), array(7,8,9,10,11,12))))) {

                $course = $this->courses[$date->format('Y-m-d')];

                if ($return === null || $course->getValue() < $return->getValue()) {
                    $return = $course;
                }
            }

            if ($halfyear == 1 && intval($date->format('m')) == 7) {
                $date->modify('+6 month');
            } else if($halfyear == 2 && intval($date->format('m') == 1)) {
                $date->modify('+6 month');
            }

            $date->modify('+1 day');
        }

        return $return;
    }

    public function getCourseAfterDrop($fromDate, $toDate, $percentageChange = 10, $days = 10)
    {
        $return = null;

        $date = clone $fromDate;
        while ($date <= $toDate && $return === null)
        {
            if (isset($this->courses[$date->format('Y-m-d')])) {
                $courseToday = $this->courses[$date->format('Y-m-d')];

                // get last 10 days and its highest course
                $highestCourse = null;
                $compareDate = clone $date;
                for ($i = 1; $i<=$days; $i++) {
                    $compareDate->modify('-1 day');

                    if (isset($this->courses[$compareDate->format('Y-m-d')])) {
                        $compareCourse = $this->courses[$compareDate->format('Y-m-d')];

                        if ($highestCourse === null || $compareCourse->getValue() > $highestCourse->getValue()) {
                            $highestCourse = $compareCourse;
                        }
                    }
                }

                // price dropped by 10%?
                if ($highestCourse !== null) {
                    $priceAfterDrop = $highestCourse->getValue() * ((100 - $percentageChange) / 100);
                    if ($courseToday->getValue() <= $priceAfterDrop) {
                        $return = $courseToday;
                    }
                }
            }

            $date->modify('+1 day');
        }

        return $return;
    }

    /**
     * @param DateTime $firstOfMonth
     * @return Course|null
     */
    public function getHighestCloseOfMonth(DateTime $firstOfMonth)
    {
        $return = null;

        $date = clone $firstOfMonth;
        while ($date->format('Y-m') == $firstOfMonth->format('Y-m'))
        {
            if (isset($this->courses[$date->format('Y-m-d')])) {
                $course = $this->courses[$date->format('Y-m-d')];

                if ($return === null || $course->getValue() > $return->getValue()) {
                    $return = $course;
                }
            }

            $date->modify('+1 day');
        }

        return $return;
    }

    /**
     * @param DateTime $anyDate
     * @param bool $fallback_future_course TRUE: When on the given date the course is not available take the course of a following day
     *                                     FALSE: When on the given date the course is not available take the course of a previous day
     * @return Course
     */
    public function getCourseOfDay(DateTime $anyDate, $fallback_future_course = true)
    {
        $return = null;

        $date = clone $anyDate;

        while ($return === null)
        {
            if (isset($this->courses[$date->format('Y-m-d')])) {
                $return = $this->courses[$date->format('Y-m-d')];
            }

            if ($fallback_future_course) {
                $date->modify('+1 day');
            } else {
                $date->modify('-1 day');
            }
        }

        return $return;
    }

    /**
     * @return Course[]
     */
    public function getCourses() {
        return $this->courses;
    }
}