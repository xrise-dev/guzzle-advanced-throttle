<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle;

use SplObjectStorage;

/**
 * Class RequestLimitGroup
 * @package hamburgscleanest\GuzzleAdvancedThrottle
 */
class RequestLimitGroup
{

    /** @var SplObjectStorage */
    private $_requestLimiters;

    /** @var int */
    private $_retryAfter = 0;

    /**
     * RequestLimitGroup constructor.
     */
    public function __construct()
    {
        $this->_requestLimiters = new SplObjectStorage();
    }

    /**
     * @return RequestLimitGroup
     */
    public static function create() : self
    {
        return new static();
    }

    /**
     * @return int
     */
    public function getRetryAfter() : int
    {
        return $this->_retryAfter;
    }

    /**
     * We have to cycle through all the limiters (no early return).
     * The timers of each limiter have to be updated despite of another limiter already preventing the request.
     *
     * @return bool
     * @throws \Exception
     */
    public function canRequest() : bool
    {
        $groupCanRequest = true;
        $this->_requestLimiters->rewind();
        while ($this->_requestLimiters->valid())
        {
            /** @var RequestLimiter $requestLimiter */
            $requestLimiter = $this->_requestLimiters->current();

            $canRequest = $requestLimiter->canRequest();
            if ($groupCanRequest && !$canRequest)
            {
                $groupCanRequest = false;
                $this->_retryAfter = $requestLimiter->getRemainingSeconds();
            }

            $this->_requestLimiters->next();
        }

        return $groupCanRequest;
    }

    /**
     * @param RequestLimiter $requestLimiter
     * @return RequestLimitGroup
     */
    public function addRequestLimiter(RequestLimiter $requestLimiter) : self
    {
        $this->_requestLimiters->attach($requestLimiter);

        return $this;
    }

    /**
     * @param RequestLimiter $requestLimiter
     * @return RequestLimitGroup
     */
    public function removeRequestLimiter(RequestLimiter $requestLimiter) : self
    {
        $this->_requestLimiters->detach($requestLimiter);

        return $this;
    }

    /**
     * @return int
     */
    public function getRequestLimiterCount() : int
    {
        return $this->_requestLimiters->count();
    }
}