<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers;

use Illuminate\Container\Container;


abstract class LaravelDriver
{
    /** @var string */
    private const DEFAULT_CACHE_PREFIX = 'throttle_cache';

    protected Container $_container;
    protected string $_driver;
    protected array $_options;
    private string $_driverStoreKey;

    public function __construct(string $driver, array $options = [])
    {
        $this->_container = new Container();
        $this->_driver = $driver;
        $this->_driverStoreKey = 'cache.stores.' . $this->_driver;
        $this->_options = $options;
    }

    public function getContainer(): Container
    {
        $this->_setConfig();
        $this->_setContainer();

        return $this->_container;
    }

    abstract protected function _setContainer(): void;

    private function _setConfig(): void
    {
        $this->_container['config'] = [
            'cache.default'        => $this->_driver,
            $this->_driverStoreKey => ['driver' => $this->_driver] + $this->_options,
            'cache.prefix'         => $this->_options['cache_prefix'] ?? self::DEFAULT_CACHE_PREFIX
        ];
    }

    protected function _setStoreValue(string $key, $value): void
    {
        $this->_container->offsetSet('config.' . $this->_driverStoreKey . '.' . $key, $value);
    }
}
