<?php
interface IsinReaderInterface
{
    /**
     * @return String
     */
    public function getIsin();

    /**
     * @return DateTime|null
     */
    public function getStartDate();

    /**
     * @return DateTime|null
     */
    public function getEndDate();

    /**
     * @return CourseInterface[]
     */
    public function getCourses();

    /**
     * @param DateTime $anyDate
     * @param bool $fallback_future_course TRUE: When on the given date the course is not available take the course of a following day
     *                                     FALSE: When on the given date the course is not available take the course of a previous day
     * @return CourseInterface
     */
    public function getCourseOfDay(DateTime $anyDate, $fallback_future_course = true);

    /**
     * @param DateTime $fromDate
     * @param DateTime $toDate
     * @param int $percentageChange
     * @param int $days
     * @return CourseInterface|null
     */
    public function getCourseAfterDrop(DateTime $fromDate, DateTime $toDate, int $percentageChange = 10, int $days = 10);

    /**
     * @param DateTime $firstOfMonth
     * @return CourseInterface|null
     */
    public function getHighestCloseOfMonth(DateTime $firstOfMonth);

    /**
     * @param DateTime $firstOfMonth
     * @return CourseInterface|null
     */
    public function getLowestCloseOfMonth(DateTime $firstOfMonth);

    /**
     * @param int $year
     * @param int $halfyear
     * @return CourseInterface|null
     */
    public function getLowestCloseOfHalfyear(int $year, int $halfyear);

}