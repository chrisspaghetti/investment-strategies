<?php

interface ConfiguratorInterface
{
    /**
     * @return String
     */
    public function getIsin(): string;

    /**
     * @return float
     */
    public function getAmountPerMonth(): float;

    /**
     * @return float
     */
    public function getBrokerCommissionAnyDayOfMonth(): float;

    /**
     * @return float
     */
    public function getBrokerCommissionFirstDayOfMonth(): float;

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime;

    /**
     * @return DateTime
     */
    public function getEndDate(): DateTime;
}