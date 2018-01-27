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
    private $_retryAfter;

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
     */
    public function canRequest() : bool
    {
        $this->_retryAfter = null;
        $groupCanRequest = true;

        /** @var RequestLimiter $requestLimiter */
        foreach ($this->_requestLimiters as $requestLimiter)
        {
            $canRequest = $requestLimiter->canRequest();
            if ($groupCanRequest && !$canRequest)
            {
                $groupCanRequest = false;
                $this->_retryAfter = $requestLimiter->getRemainingSeconds();
            }
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
}