<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Middleware;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\CacheStrategy;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimitRuleset;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Class ThrottleMiddleware
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Middleware
 */
class ThrottleMiddleware
{

    /** @var \hamburgscleanest\GuzzleAdvancedThrottle\RequestLimitGroup */
    private $_requestLimitGroup;
    /** @var CacheStrategy|null */
    private $_cacheStrategy;

    /**
     * ThrottleMiddleware constructor.
     * @param RequestLimitRuleset $requestLimitRuleset
     * @param CacheStrategy|null $cacheStrategy
     * @throws \Exception
     */
    public function __construct(RequestLimitRuleset $requestLimitRuleset, CacheStrategy $cacheStrategy = null)
    {
        $this->_requestLimitGroup = $requestLimitRuleset->getRequestLimitGroup();
        $this->_cacheStrategy = $cacheStrategy;
    }

    /**
     * @return callable
     * @throws \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException
     * @throws \Exception
     */
    public function handle() : callable
    {
        return function(callable $handler) : callable
        {
            return function(RequestInterface $request, array $options) use ($handler)
            {
                if ($this->_cacheStrategy !== null)
                {
                    return $this->_cacheStrategy->request($request, $this->_requestHandler($handler, $request, $options));
                }

                return $this->_requestHandler($handler, $request, $options)();
            };
        };
    }

    /**
     * @param callable $handler
     * @param RequestInterface $request
     * @param array $options
     * @return callable
     * @throws \Exception
     */
    private function _requestHandler(callable $handler, RequestInterface $request, array $options) : callable
    {
        return function() use ($handler, $request, $options)
        {
            if (!$this->_requestLimitGroup->canRequest())
            {
                throw new TooManyRequestsHttpException(
                    $this->_requestLimitGroup->getRetryAfter(),
                    'The rate limit was exceeded. Please try again later.'
                );
            }

            return $handler($request, $options);
        };
    }
}