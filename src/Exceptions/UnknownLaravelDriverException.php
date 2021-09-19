<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

class UnknownLaravelDriverException extends \RuntimeException
{
    public function __construct(string $driverName, array $availableDrivers)
    {
        parent::__construct('Unknown Laravel driver "' . $driverName . '".' . \PHP_EOL . 'Available drivers: ' . \implode(', ', $availableDrivers));
    }
}
