<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters;

use DateTime;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers\CacheConfigHelper;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers\RequestHelper;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheConfigNotSetException;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class LaravelAdapter
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters
 */
class LaravelAdapter implements StorageInterface
{

    /** @var int */
    private const DEFAULT_TTL = 300;
    /** @var string */
    private const STORAGE_KEY = 'requests';
    /** @var CacheManager */
    private $_cacheManager;
    /** @var int Time To Live in minutes */
    private $_ttl;

    /**
     * LaravelAdapter constructor.
     * @param Repository|null $config
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheDriverNotSetException
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheConfigNotSetException
     */
    public function __construct(?Repository $config = null)
    {
        if ($config === null || ($cacheConfig = $config->get('cache')) === null)
        {
            throw new LaravelCacheConfigNotSetException();
        }

        $cacheRepository = new Repository($cacheConfig);
        $this->_cacheManager = CacheConfigHelper::getCacheManager($cacheRepository);
        $this->_ttl = $cacheRepository->get('ttl', self::DEFAULT_TTL);
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
     * @throws \Exception
     */
    public function saveResponse(RequestInterface $request, ResponseInterface $response) : void
    {
        $this->_cacheManager->put($this->_buildResponseKey($request), $response, $this->_ttl);
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function _buildResponseKey(RequestInterface $request) : string
    {
        [$host, $path] = RequestHelper::getHostAndPath($request);

        return self::STORAGE_KEY . '.' . $host . '.' . $path . '.' . RequestHelper::getStorageKey($request);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null
     */
    public function getResponse(RequestInterface $request) : ? ResponseInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->_cacheManager->get($this->_buildResponseKey($request));
    }
}