<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\ArrayAdapter;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\Helpers\RequestHelper;
use hamburgscleanest\GuzzleAdvancedThrottle\Helpers\UrlHelper;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;

class RequestLimiter
{
    /** @var int */
    private const DEFAULT_MAX_REQUESTS = 120;
    /** @var int */
    private const DEFAULT_REQUEST_INTERVAL = 60;

    private string $_host;
    private TimeKeeper $_timekeeper;
    private int $_requestCount = 0;
    private int $_maxRequestCount = self::DEFAULT_MAX_REQUESTS;
    private StorageInterface $_storage;
    private string $_storageKey;

    public function __construct(string $host, ?int $maxRequests = self::DEFAULT_MAX_REQUESTS, ?int $requestIntervalSeconds = self::DEFAULT_REQUEST_INTERVAL, StorageInterface $storage = null)
    {
        $this->_storage = $storage ?? new ArrayAdapter();
        $this->_host = UrlHelper::removeTrailingSlash($host);
        $this->_maxRequestCount = $maxRequests ?? self::DEFAULT_MAX_REQUESTS;

        $requestInterval = $requestIntervalSeconds ?? self::DEFAULT_REQUEST_INTERVAL;

        $this->_storageKey = $this->_maxRequestCount . '_' . $requestInterval;
        $this->_timekeeper = new TimeKeeper($requestInterval);

        $this->_restoreState();
    }

    private function _restoreState(): void
    {
        $requestInfo = $this->_storage->get($this->_host, $this->_storageKey);
        if ($requestInfo === null) {
            return;
        }

        $this->_requestCount = $requestInfo->requestCount;
        if ($requestInfo->expiresAt !== null) {
            $this->_timekeeper->setExpiration($requestInfo->expiresAt);
        }
    }

    public static function createFromRule(string $host, array $rule, StorageInterface $storage = null): self
    {
        return new static($host, $rule['max_requests'] ?? null, $rule['request_interval'] ?? null, $storage);
    }

    public static function create(string $host, ?int $maxRequests = self::DEFAULT_MAX_REQUESTS, ?int $requestIntervalSeconds = self::DEFAULT_REQUEST_INTERVAL, StorageInterface $storage = null): self
    {
        return new static($host, $maxRequests, $requestIntervalSeconds, $storage);
    }

    public function canRequest(RequestInterface $request, array $options = []): bool
    {
        if (!$this->matches(RequestHelper::getHostFromRequestAndOptions($request, $options))) {
            return true;
        }

        if (++$this->_requestCount === 1) {
            $this->_timekeeper->start();
        }

        if ($this->_timekeeper->isExpired()) {
            $this->_timekeeper->reset();
            $this->_requestCount = 0;
        }

        if ($this->_requestCount > $this->_maxRequestCount) {
            return false;
        }

        $this->_storage->save(
            $this->_host,
            $this->_storageKey,
            $this->_requestCount,
            $this->_timekeeper
        );

        return true;
    }

    public function matches(string $host): bool
    {
        return $host === $this->_host || Str::startsWith($host, $this->_host) || Wildcard::matches($this->_host, $host);
    }

    public function getRemainingSeconds(): int
    {
        return $this->_timekeeper->getRemainingSeconds();
    }

    public function getCurrentRequestCount(): int
    {
        return $this->_requestCount;
    }
}
