<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Strategies;

use GuzzleHttp\Promise\PromiseInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\CacheStrategy;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use Psr\Http\Message\RequestInterface;

class NoCache implements CacheStrategy
{
    public function __construct(StorageInterface $storage = null)
    {
        // No caching, so don't do anything with the storage..
    }

    public function request(RequestInterface $request, callable $handler): PromiseInterface
    {
        return $handler();
    }
}
