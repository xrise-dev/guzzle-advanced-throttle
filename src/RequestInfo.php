<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle;

use DateTime;

class RequestInfo
{
    public int $requestCount;
    public DateTime $expiresAt;
    public int $remainingSeconds;

    public function __construct(int $requestCount, int $expirationTimestamp, int $remainingSeconds)
    {
        $this->requestCount = $requestCount;
        $this->expiresAt = (new DateTime())->setTimestamp($expirationTimestamp);
        $this->remainingSeconds = $remainingSeconds;
    }

    public static function create(int $requestCount, int $expirationTimestamp, int $remainingSeconds): RequestInfo
    {
        return new static($requestCount, $expirationTimestamp, $remainingSeconds);
    }
}
