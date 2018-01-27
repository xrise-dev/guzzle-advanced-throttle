<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters;

use DateTime;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;
use Illuminate\Support\Facades\Cache;

/**
 * Class LaravelAdapter
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters
 */
class LaravelAdapter implements StorageInterface
{

    /** @var array */
    private $_storage = [];

    /**
     * @param string $host
     * @param string $key
     * @param int $requestCount
     * @param DateTime $expiresAt
     * @param int $remainingSeconds
     */
    public function save(string $host, string $key, int $requestCount, DateTime $expiresAt, int $remainingSeconds) : void
    {
        Cache::put($this->_buildKey($host, $key), 'value', $expiresAt);

        $this->_storage[$host][$key] = RequestInfo::create($requestCount, $expiresAt->getTimestamp(), $remainingSeconds);
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
        return Cache::get($this->_buildKey($host, $key));
    }
}