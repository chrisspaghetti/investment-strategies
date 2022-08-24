<?php
interface CourseInterface
{
    /**
     * @return DateTime
     */
    public function getDate();

    /**
     * @return float
     */
    public function getValue();
}