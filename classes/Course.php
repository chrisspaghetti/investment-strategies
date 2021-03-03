<?php


class Course
{
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var float
     */
    protected $value;

    /**
     * Konstruktur
     * @param float $course
     * @param DateTime $date
     */
    public function __construct(float $value, DateTime $date)
    {
        $this->value = $value;
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getValue()
    {
        return $this->value;
    }
}