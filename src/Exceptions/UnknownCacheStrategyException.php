<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\CacheStrategy;
use hamburgscleanest\GuzzleAdvancedThrottle\Helpers\InterfaceHelper;

class UnknownCacheStrategyException extends \RuntimeException
{
    public function __construct(string $cacheStrategy, array $additionalStrategies = [])
    {
        parent::__construct(
            'Unknown cache strategy "' . $cacheStrategy . '".' . \PHP_EOL .
                'Available adapters: ' . \implode(', ', $additionalStrategies + InterfaceHelper::getImplementations(CacheStrategy::class))
        );
    }
}
