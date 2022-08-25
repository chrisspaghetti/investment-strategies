<?php
interface StrategyInterface
{
    /**
     * apply the strategy to the given portfolio and invest cash within the given time range
     *
     * @param PortfolioInterface $portfolio
     * @param IsinReaderInterface $isinReader
     * @param TimeRangeInterface $timeRange
     * @param ConfiguratorInterface $configurator
     * @throws CalculationException
     */
    public function applyToPortfolio(PortfolioInterface $portfolio,
                                     IsinReaderInterface $isinReader,
                                     TimeRangeInterface $timeRange,
                                     ConfiguratorInterface $configurator);
}