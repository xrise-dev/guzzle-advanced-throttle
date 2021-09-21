<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use Psr\Http\Message\RequestInterface;
use SplObjectStorage;

class RequestLimitGroup
{
    private SplObjectStorage $_requestLimiters;
    private int $_retryAfter = 0;

    public function __construct()
    {
        $this->_requestLimiters = new SplObjectStorage();
    }

    public static function create(): self
    {
        return new static();
    }

    public function getRetryAfter(): int
    {
        return $this->_retryAfter;
    }

    /**
     * We have to cycle through all the limiters (no early return).
     * The timers of each limiter have to be updated despite of another limiter already preventing the request.
     */
    public function canRequest(RequestInterface $request, array $options = []): bool
    {
        $groupCanRequest = true;
        $this->_requestLimiters->rewind();
        while ($this->_requestLimiters->valid()) {
            /** @var RequestLimiter $requestLimiter */
            $requestLimiter = $this->_requestLimiters->current();

            $canRequest = $requestLimiter->canRequest($request, $options);

            if ($groupCanRequest && !$canRequest) {
                $groupCanRequest = false;
                $this->_retryAfter = $requestLimiter->getRemainingSeconds();
            }

            $this->_requestLimiters->next();
        }

        return $groupCanRequest;
    }

    public function addRequestLimiter(RequestLimiter $requestLimiter): self
    {
        $this->_requestLimiters->attach($requestLimiter);

        return $this;
    }

    public function removeRequestLimiter(RequestLimiter $requestLimiter): self
    {
        $this->_requestLimiters->detach($requestLimiter);

        return $this;
    }

    public function getRequestLimiterCount(): int
    {
        return $this->_requestLimiters->count();
    }

    public function addRules(string $host, array $rules, StorageInterface $storage): void
    {
        foreach ($rules as $rule) {
            $this->addRequestLimiter(
                RequestLimiter::createFromRule(
                    $host,
                    $rule,
                    $storage
                )
            );
        }
    }
}
