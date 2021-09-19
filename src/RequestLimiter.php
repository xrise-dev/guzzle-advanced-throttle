<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle;

use GuzzleHttp\Psr7\Uri;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\ArrayAdapter;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
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
    private int $_maxRequestCount;
    private StorageInterface $_storage;
    private string $_storageKey;

    public function __construct(string $host, ?int $maxRequests = self::DEFAULT_MAX_REQUESTS, ?int $requestIntervalSeconds = self::DEFAULT_REQUEST_INTERVAL, StorageInterface $storage = null)
    {
        $this->_storage = $storage ?? new ArrayAdapter();
        $this->_host = \rtrim($host, '/');
        $this->_maxRequestCount = $maxRequests ?? self::DEFAULT_MAX_REQUESTS;
        $requestInterval = $requestIntervalSeconds ?? self::DEFAULT_REQUEST_INTERVAL;

        $this->_storageKey = $this->_maxRequestCount . '_' . $requestInterval;
        $this->_restoreState($requestInterval);
    }

    private function _restoreState(int $requestIntervalSeconds): void
    {
        $this->_timekeeper = new TimeKeeper($requestIntervalSeconds);

        $requestInfo = $this->_storage->get($this->_host, $this->_storageKey);
        if ($requestInfo === null) {
            return;
        }

        $this->_requestCount = $requestInfo->requestCount;
        $this->_timekeeper->setExpiration($requestInfo->expiresAt);
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
        if (!$this->matches($this->_getHostFromRequestAndOptions($request, $options))) {
            return true;
        }

        if ($this->getCurrentRequestCount() >= $this->_maxRequestCount) {
            return false;
        }

        $this->_increment();
        $this->_save();

        return true;
    }

    public function matches(string $host): bool
    {
        return $host === $this->_host || Str::startsWith($host, $this->_host) || Wildcard::matches($this->_host, $host);
    }

    private function _getHostFromRequestAndOptions(RequestInterface $request, array $options = []): string
    {
        $uri = $options['base_uri'] ?? $request->getUri();

        return $this->_buildHostUrl($uri);
    }

    private function _buildHostUrl(Uri $uri): string
    {
        $host = $uri->getHost() . $uri->getPath();
        $scheme = $uri->getScheme();

        if (!empty($scheme)) {
            return $scheme . '://' . $host;
        }

        return $host;
    }

    /**
     * Increment the request counter.
     */
    private function _increment(): void
    {
        $this->_requestCount++;
        if ($this->_requestCount === 1) {
            $this->_timekeeper->start();
        }
    }

    /**
     * Save timer in storage
     */
    private function _save(): void
    {
        $this->_storage->save(
            $this->_host,
            $this->_storageKey,
            $this->_requestCount,
            $this->_timekeeper->getExpiration(),
            $this->getRemainingSeconds()
        );
    }

    public function getRemainingSeconds(): int
    {
        return $this->_timekeeper->getRemainingSeconds();
    }

    public function getCurrentRequestCount(): int
    {
        if ($this->_timekeeper->isExpired()) {
            $this->_timekeeper->reset();
            $this->_requestCount = 0;
        }

        return $this->_requestCount;
    }
}
