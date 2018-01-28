<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\ArrayAdapter;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\HostNotDefinedException;

/**
 * Class RequestLimiter
 * @package hamburgscleanest\GuzzleAdvancedThrottle
 */
class RequestLimiter
{

    /** @var int */
    private const DEFAULT_MAX_REQUESTS = 120;
    /** @var int */
    private const DEFAULT_REQUEST_INTERVAL = 60;
    /** @var string */
    private $_host;
    /** @var TimeKeeper */
    private $_timekeeper;
    /** @var int */
    private $_requestCount = 0;
    /** @var int */
    private $_maxRequestCount;
    /** @var StorageInterface */
    private $_storage;
    /** @var string */
    private $_storageKey;

    /**
     * RequestLimiter constructor.
     * @param string $host
     * @param int $maxRequests
     * @param int $requestIntervalSeconds
     * @param StorageInterface|null $storage
     * @throws \Exception
     */
    public function __construct(string $host, ?int $maxRequests = self::DEFAULT_MAX_REQUESTS, ?int $requestIntervalSeconds = self::DEFAULT_REQUEST_INTERVAL, StorageInterface $storage = null)
    {
        $this->_storage = $storage ?? new ArrayAdapter();
        $this->_host = $host;
        $this->_maxRequestCount = $maxRequests ?? self::DEFAULT_MAX_REQUESTS;
        $requestInterval = $requestIntervalSeconds ?? self::DEFAULT_REQUEST_INTERVAL;

        $this->_storageKey = $maxRequests . '_' . $requestInterval;
        $this->_restoreState($requestInterval);
    }

    /**
     * @param int $requestIntervalSeconds
     * @throws \Exception
     */
    private function _restoreState(int $requestIntervalSeconds) : void
    {
        $this->_timekeeper = new TimeKeeper($requestIntervalSeconds);

        $requestInfo = $this->_storage->get($this->_host, $this->_storageKey);
        if ($requestInfo !== null)
        {
            $this->_requestCount = $requestInfo->requestCount;
            $this->_timekeeper->setExpiration($requestInfo->expiresAt);
        }
    }

    /**
     * @param array $rule
     * @param StorageInterface|null $storage
     * @return RequestLimiter
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\HostNotDefinedException
     * @throws \Exception
     */
    public static function createFromRule(array $rule, StorageInterface $storage = null) : self
    {
        if (!isset($rule['host']))
        {
            throw new HostNotDefinedException();
        }

        return new static($rule['host'], $rule['max_requests'] ?? null, $rule['request_interval'] ?? null, $storage);
    }

    /**
     * @param string $host
     * @param int $maxRequests
     * @param int $requestIntervalSeconds
     * @param StorageInterface|null $storage
     * @return RequestLimiter
     * @throws \Exception
     */
    public static function create(string $host, ?int $maxRequests = self::DEFAULT_MAX_REQUESTS, ?int $requestIntervalSeconds = self::DEFAULT_REQUEST_INTERVAL, StorageInterface $storage = null) : self
    {
        return new static($host, $maxRequests, $requestIntervalSeconds, $storage);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function canRequest() : bool
    {
        if ($this->_requestCount >= $this->_maxRequestCount)
        {
            return false;
        }

        $this->_increment();
        $this->_save();

        return true;
    }

    /**
     * Increment the request counter.
     * @throws \Exception
     */
    private function _increment() : void
    {
        $this->_requestCount ++;
        if ($this->_requestCount === 1)
        {
            $this->_timekeeper->start();
        }
    }

    /**
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\TimerNotStartedException
     */
    private function _save() : void
    {
        $this->_storage->save(
            $this->_host,
            $this->_storageKey,
            $this->_requestCount,
            $this->_timekeeper->getExpiration(),
            $this->getRemainingSeconds()
        );
    }

    /**
     * @return int
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\TimerNotStartedException
     */
    public function getRemainingSeconds() : ? int
    {
        return $this->_timekeeper->getRemainingSeconds();
    }

    /**
     * @return int
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\TimerNotStartedException
     */
    public function getCurrentRequestCount() : int
    {
        if ($this->_timekeeper->isExpired())
        {
            $this->_timekeeper->reset();
            $this->_requestCount = 0;
        }

        return $this->_requestCount;
    }
}