<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers\FileDriver;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers\LaravelDriver;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers\RedisDriver;
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

    /** @var array */
    private $drivers = [
        'file'  => FileDriver::class,
        'redis' => RedisDriver::class
    ];

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

        /** @var LaravelDriver $driverClass */
        $driverClass = new self::$this->drivers[$driver](
            $driver,
            $config['options'] ?? []
        );

        return $driverClass->getContainer();
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