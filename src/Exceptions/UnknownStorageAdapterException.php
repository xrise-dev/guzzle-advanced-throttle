<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\Helpers\InterfaceHelper;

class UnknownStorageAdapterException extends \RuntimeException
{
    public function __construct(string $adapterName, array $additionalAdapters)
    {
        parent::__construct(
            'Unknown storage adapter "' . $adapterName . '".' . \PHP_EOL .
                'Available adapters: ' . \implode(', ', $additionalAdapters + InterfaceHelper::getImplementations(StorageInterface::class))
        );
    }
}
