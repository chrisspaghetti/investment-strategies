<?php


class Course implements CourseInterface
{
    /**
     * @var DateTime
     */
    protected DateTime $date;

    /**
     * @var float
     */
    protected float $value;

    /**
     * constructor for a course
     * @param float $value
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
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
}