<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters;

use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\CachedResponse;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;
use hamburgscleanest\GuzzleAdvancedThrottle\SystemClock;
use hamburgscleanest\GuzzleAdvancedThrottle\TimeKeeper;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Config\Repository;

class ArrayAdapter extends BaseAdapter
{
    /** @var string */
    private const RESPONSE_KEY = 'response';
    /** @var string */
    private const EXPIRATION_KEY = 'expires_at';

    private array $_storage = [];

    public function __construct(?Repository $config = null)
    {
        if ($config === null) {
            return;
        }

        $this->_ttl = $config->get('cache.ttl', self::DEFAULT_TTL);
        $this->_allowEmptyValues = $config->get('cache.allow_empty', $this->_allowEmptyValues);
    }

    public function save(string $host, string $key, int $requestCount, TimeKeeper $timeKeeper): void
    {
        $expiration = $timeKeeper->getExpiration();
        if ($expiration === null) {
            unset($this->_storage[$host][$key]);
            return;
        }

        $this->_storage[$host][$key] = RequestInfo::create(
            $requestCount,
            $expiration->getTimestamp(),
            $timeKeeper->getRemainingSeconds()
        );
    }

    public function get(string $host, string $key): ?RequestInfo
    {
        return $this->_storage[$host][$key] ?? null;
    }

    protected function _saveResponse(ResponseInterface $response, string $host, string $path, string $key): void
    {
        $this->_storage[self::STORAGE_KEY][$host][$path][$key] = [
            self::RESPONSE_KEY   => new CachedResponse($response),
            self::EXPIRATION_KEY => SystemClock::create()->advanceMinutes($this->_ttl)->now()->getTimestamp()
        ];
    }

    protected function _getResponse(string $host, string $path, string $key): ?Response
    {
        $response = $this->_storage[self::STORAGE_KEY][$host][$path][$key] ?? null;

        if ($response !== null) {
            if ($response[self::EXPIRATION_KEY] > \time()) {
                /** @var CachedResponse|null $cachedResponse */
                $cachedResponse = $response[self::RESPONSE_KEY];

                return $cachedResponse ? $cachedResponse->getResponse() : null;
            }

            unset($this->_storage[self::STORAGE_KEY][$host][$path][$key]);
        }

        return null;
    }
}
