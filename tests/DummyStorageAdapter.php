<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateTimeImmutable;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;
use Illuminate\Config\Repository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DummyStorageAdapter implements StorageInterface
{
    public function __construct(?Repository $config = null)
    {
    }

    public function save(string $host, string $key, int $requestCount, DateTimeImmutable $expiresAt, int $remainingSeconds): void
    {
    }

    /**
     * @param string $host
     * @param string $key
     * @return RequestInfo|null
     */
    public function get(string $host, string $key): ?RequestInfo
    {
        return null;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function saveResponse(RequestInterface $request, ResponseInterface $response): void
    {
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null
     */
    public function getResponse(RequestInterface $request): ?ResponseInterface
    {
        return null;
    }
}
