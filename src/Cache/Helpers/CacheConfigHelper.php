<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers\LaravelDriver;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheDriverNotSetException;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;


/**
 * Class CacheConfigHelper
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers
 */
class CacheConfigHelper
{

    /**
     * @param Repository $config
     * @return CacheManager
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheDriverNotSetException
     */
    public static function getCacheManager(Repository $config) : CacheManager
    {
        return new CacheManager(self::getContainer($config));
    }

    /**
     * @param Repository $config
     * @return Container
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheDriverNotSetException
     */
    public static function getContainer(Repository $config) : Container
    {
        $driver = self::getDriver($config);

        $container = new Container();

        if ($driver === 'file')
        {
            $container['files'] = new Filesystem();
        }

        $container['config'] = LaravelDriver::getDriverConfig($driver, $config['options'] ?? []);

        return $container;
    }

    /**
     * @param Repository $config
     * @return string
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheDriverNotSetException
     */
    public static function getDriver(Repository $config) : string
    {
        $driver = $config->get('driver');

        if ($driver === null)
        {
            throw new LaravelCacheDriverNotSetException();
        }

        return $driver;
    }
}