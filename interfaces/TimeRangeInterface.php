<?php

interface TimeRangeInterface
{
    /**
     * @return DateTime[]
     */
    public function getMonths();

    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
     */
    public function getEndDate();
}