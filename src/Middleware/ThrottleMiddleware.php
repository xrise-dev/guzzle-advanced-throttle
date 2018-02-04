<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Middleware;

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

    /**
     * ThrottleMiddleware constructor.
     * @param RequestLimitRuleset $requestLimitRuleset
     * @throws \Exception
     */
    public function __construct(RequestLimitRuleset $requestLimitRuleset)
    {
        $this->_requestLimitGroup = $requestLimitRuleset->getRequestLimitGroup();
    }

    /**
     * @return callable
     * @throws \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException
     */
    public function handle() : callable
    {
        return function (callable $handler): callable {
            return function (RequestInterface $request, array $options) use ($handler) : callable {
                if (!$this->_requestLimitGroup->canRequest()) {
                    throw new TooManyRequestsHttpException(
                        $this->_requestLimitGroup->getRetryAfter(),
                        'The rate limit was exceeded. Please try again later.'
                    );
                }

                return $handler($request, $options);
            };
        };
    }
}