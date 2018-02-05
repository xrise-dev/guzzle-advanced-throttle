<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

/**
 * Class UnknownCacheStrategyException
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Exceptions
 */
class UnknownCacheStrategyException extends \RuntimeException
{

    /**
     * UnknownCacheStrategyException constructor.
     * @param string $cacheStrategy
     * @param array $availableStrategies
     */
    public function __construct(string $cacheStrategy, array $availableStrategies)
    {
        parent::__construct('Unknown cache strategy "' . $cacheStrategy . '".' . \PHP_EOL . 'Available adapters: ' . \implode(', ', $availableStrategies));
    }
}