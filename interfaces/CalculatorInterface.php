<?php

interface CalculatorInterface
{
    /**
     * @return CalculationResultInterface
     * @throws CalculationException
     */
    public function calc(): CalculationResultInterface;

}