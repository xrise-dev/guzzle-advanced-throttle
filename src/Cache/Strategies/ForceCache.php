<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Strategies;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;

class ForceCache extends Cacheable
{
    public function request(RequestInterface $request, callable $handler): PromiseInterface
    {
        $response = $this->_getResponse($request);
        if ($response !== null) {
            return new FulfilledPromise($response);
        }

        return parent::request($request, $handler);
    }
}
