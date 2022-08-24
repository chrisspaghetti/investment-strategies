<?php

interface ConfiguratorInterface
{
    /**
     * @return String
     */
    public function getIsin();

    /**
     * @return float
     */
    public function getAmountPerMonth();

    /**
     * @return float
     */
    public function getBrokerCommissionAnyDayOfMonth();

    /**
     * @return float
     */
    public function getBrokerCommissionFirstDayOfMonth();

    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
     */
    public function getEndDate();

}