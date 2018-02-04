<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters;

use DateTime;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;
use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;

/**
 * Class LaravelAdapter
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters
 */
class LaravelAdapter implements StorageInterface
{

    /** @var CacheManager */
    private $_cacheManager;

    public function __construct()
    {
        $container = new Container();

        // TODO: Set from outside..
        $container['config'] = [
            'cache.default'     => 'file',
            'cache.stores.file' => [
                'driver' => 'file',
                'path'   => __DIR__ . '/cache'
            ]
        ];
        $container['files'] = new Filesystem(); // TODO: Remove when config extracted..

        $this->_cacheManager = new CacheManager($container);
    }

    /**
     * @param string $host
     * @param string $key
     * @param int $requestCount
     * @param DateTime $expiresAt
     * @param int $remainingSeconds
     */
    public function save(string $host, string $key, int $requestCount, DateTime $expiresAt, int $remainingSeconds) : void
    {
        $this->_cacheManager->put(
            $this->_buildKey($host, $key),
            RequestInfo::create($requestCount, $expiresAt->getTimestamp(), $remainingSeconds),
            $remainingSeconds
        );
    }

    /**
     * @param string $host
     * @param string $key
     * @return string
     */
    private function _buildKey(string $host, string $key) : string
    {
        return $host . '.' . $key;
    }

    /**
     * @param string $host
     * @param string $key
     * @return RequestInfo|null
     */
    public function get(string $host, string $key) : ? RequestInfo
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->_cacheManager->get($this->_buildKey($host, $key));
    }
}