<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use GuzzleHttp\Promise\PromiseInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\CacheStrategy;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use Psr\Http\Message\RequestInterface;

class DummyCacheStrategy implements CacheStrategy
{
    public function __construct(?StorageInterface $storage = null)
    {
    }

    public function request(RequestInterface $request, callable $handler): PromiseInterface
    {
        return $handler();
    }
}
