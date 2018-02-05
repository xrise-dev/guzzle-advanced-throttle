<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

/**
 * Class UnknownStorageAdapterException
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Exceptions
 */
class UnknownStorageAdapterException extends \RuntimeException
{

    /**
     * UnknownStorageAdapterException constructor.
     * @param string $adapterName
     * @param array $availableStrategies
     */
    public function __construct(string $adapterName, array $availableStrategies)
    {
        parent::__construct('Unknown storage adapter "' . $adapterName . '".' . \PHP_EOL . 'Available adapters: ' . \implode(', ', $availableStrategies));
    }
}