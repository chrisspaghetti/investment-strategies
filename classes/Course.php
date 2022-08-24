<?php


class Course implements CourseInterface
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
     * constructor for a course
     * @param float $course
     * @param DateTime $date
     */
    public function __construct(float $value, DateTime $date)
    {
        $this->value = $value;
        $this->date = $date;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }
}