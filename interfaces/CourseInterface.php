<?php
interface CourseInterface
{
    /**
     * @return DateTime
     */
    public function getDate(): DateTime;

    /**
     * @return float
     */
    public function getValue(): float;
}