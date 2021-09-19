<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;


interface CacheStrategy
{
    public function __construct(StorageInterface $storage = null);

    public function request(RequestInterface $request, callable $handler): PromiseInterface;
}
