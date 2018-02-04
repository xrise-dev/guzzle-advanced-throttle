<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

/**
 * Class UnknownStorageAdapterException
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Exceptions
 */
class UnknownStorageAdapterException extends \RuntimeException
{

    /**
     * HostNotDefinedException constructor.
     * @param string $adapterName
     * @param array $availableAdapters
     */
    public function __construct(string $adapterName, array $availableAdapters)
    {
        parent::__construct('Unknown storage adapter "' . $adapterName . '".' . \PHP_EOL . 'Available adapters: ' . \implode(', ', $availableAdapters));
    }
}