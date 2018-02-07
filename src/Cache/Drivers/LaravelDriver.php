<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers;


/**
 * Class LaravelDriver
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers
 */
class LaravelDriver
{


    /**
     * @param string $driver
     * @param array $options
     * @return array
     */
    public static function getDriverConfig(string $driver, array $options) : array
    {
        return [
            'cache.default'           => $driver,
            'cache.stores.' . $driver => ['driver' => $driver] + $options
        ];
    }
}