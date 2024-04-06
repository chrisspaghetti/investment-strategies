<?php
interface IsinReaderInterface
{
    /**
     * @return String
     */
    public function getCurrency(): string;

    /**
     * @return String
     */
    public function getIsin(): string;

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime;

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime;

    /**
     * @return CourseInterface[]
     */
    public function getCourses(): array;

    /**
     * @param DateTime $anyDate
     * @param bool $fallback_future_course TRUE: When on the given date the course is not available take the course of a following day
     *                                     FALSE: When on the given date the course is not available take the course of a previous day
     * @return CourseInterface
     */
    public function getCourseOfDay(DateTime $anyDate, $fallback_future_course = true): CourseInterface;

    /**
     * @param DateTime $fromDate
     * @param DateTime $toDate
     * @param int $percentageChange
     * @param int $days
     * @return CourseInterface|null
     */
    public function getCourseAfterDrop(DateTime $fromDate, DateTime $toDate, int $percentageChange = 10, int $days = 10): ?CourseInterface;

    /**
     * @param DateTime $firstOfMonth
     * @return CourseInterface|null
     */
    public function getHighestCloseOfMonth(DateTime $firstOfMonth): ?CourseInterface;

    /**
     * @param DateTime $firstOfMonth
     * @return CourseInterface|null
     */
    public function getLowestCloseOfMonth(DateTime $firstOfMonth): ?CourseInterface;

    /**
     * @param int $year
     * @param int $halfyear
     * @return CourseInterface|null
     */
    public function getLowestCloseOfHalfyear(int $year, int $halfyear): ?CourseInterface;

}