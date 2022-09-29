<?php

interface TimeRangeInterface
{
    /**
     * @return DateTime[]
     */
    public function getMonths(): array;

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime;

    /**
     * @return DateTime
     */
    public function getEndDate(): DateTime;
}