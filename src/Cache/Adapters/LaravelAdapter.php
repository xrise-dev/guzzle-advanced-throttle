<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters;

use DateTime;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers\RequestHelper;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheConfigNotSetException;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class LaravelAdapter
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters
 */
class LaravelAdapter implements StorageInterface
{

    /** @var string */
    private const STORAGE_KEY = 'requests';
    /** @var CacheManager */
    private $_cacheManager;

    /**
     * LaravelAdapter constructor.
     * @param Repository|null $config
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheConfigNotSetException
     */
    public function __construct(?Repository $config = null)
    {
        $this->_cacheManager = $this->_getCacheManager($config);
    }

    /**
     * @param Repository|null $config
     * @return CacheManager
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheConfigNotSetException
     */
    private function _getCacheManager(Repository $config = null) : CacheManager
    {
        /** @var Repository $storageConfig */
        $storageConfig = null;
        if ($config === null || ($storageConfig = new Repository($config->get('storage'))) === null)
        {
            throw new LaravelCacheConfigNotSetException();
        }

        $container = new Container();
        $store = $storageConfig->get('cache.stores.file');
        if ($store !== null && $store['driver'] === 'file')
        {
            $container['files'] = new Filesystem();
        }

        $container['config'] = $storageConfig->all();

        return new CacheManager($container);
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
            $remainingSeconds / 60
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

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param int $duration
     * @throws \Exception
     */
    public function saveResponse(RequestInterface $request, ResponseInterface $response, int $duration = 300) : void
    {
        [$host, $path] = RequestHelper::getHostAndPath($request);

        $this->_cacheManager->put($this->_buildResponseKey($host, $path), $response, $duration);
    }

    /**
     * @param string $host
     * @param string $path
     * @return string
     */
    private function _buildResponseKey(string $host, string $path) : string
    {
        return self::STORAGE_KEY . '.' . $this->_buildKey($host, $path);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null
     */
    public function getResponse(RequestInterface $request) : ? ResponseInterface
    {
        [$host, $path] = RequestHelper::getHostAndPath($request);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->_cacheManager->get($this->_buildResponseKey($host, $path));
    }
}