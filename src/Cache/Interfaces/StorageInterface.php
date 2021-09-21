<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces;

use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;
use hamburgscleanest\GuzzleAdvancedThrottle\TimeKeeper;
use Illuminate\Config\Repository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface StorageInterface
{
    public function __construct(?Repository $config = null);

    public function save(string $host, string $key, int $requestCount, TimeKeeper $timeKeeper): void;

    public function get(string $host, string $key): ?RequestInfo;

    public function saveResponse(RequestInterface $request, ResponseInterface $response): void;

    public function getResponse(RequestInterface $request): ?ResponseInterface;
}
