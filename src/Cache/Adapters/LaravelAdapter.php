<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\CachedResponse;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers\CacheConfigHelper;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheConfigNotSetException;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Psr\Http\Message\ResponseInterface;

class LaravelAdapter extends BaseAdapter
{
    private CacheManager $_cacheManager;

    public function __construct(?Repository $config = null)
    {
        if ($config === null || ($cacheConfig = $config->get('cache')) === null) {
            throw new LaravelCacheConfigNotSetException();
        }

        $cacheRepository = new Repository($cacheConfig);
        $this->_cacheManager = CacheConfigHelper::getCacheManager($cacheRepository);
        $this->_ttl = $cacheRepository->get('ttl', self::DEFAULT_TTL);
        $this->_allowEmptyValues = $cacheRepository->get('allow_empty', $this->_allowEmptyValues);
    }

    public function save(string $host, string $key, int $requestCount, DateTimeImmutable $expiresAt, int $remainingSeconds): void
    {
        $this->_cacheManager->put(
            $this->_buildKey($host, $key),
            RequestInfo::create($requestCount, $expiresAt->getTimestamp(), $remainingSeconds),
            $remainingSeconds
        );
    }

    private function _buildKey(string $host, string $key): string
    {
        return $host . '.' . $key;
    }

    public function get(string $host, string $key): ?RequestInfo
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->_cacheManager->get($this->_buildKey($host, $key));
    }

    protected function _saveResponse(ResponseInterface $response, string $host, string $path, string $key): void
    {
        $this->_cacheManager->put(
            $this->_buildResponseKey($host, $path, $key),
            new CachedResponse($response),
            $this->_ttl
        );
    }

    private function _buildResponseKey(string $host, string $path, string $key): string
    {
        return self::STORAGE_KEY . '.' . $host . '.' . $path . '.' . $key;
    }

    protected function _getResponse(string $host, string $path, string $key): ?Response
    {
        /** @var CachedResponse|null $cachedResponse */
        $cachedResponse = $this->_cacheManager->get($this->_buildResponseKey($host, $path, $key));

        return $cachedResponse ? $cachedResponse->getResponse() : null;
    }
}
