<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateTimeImmutable;
use hamburgscleanest\GuzzleAdvancedThrottle\Interfaces\Clock;
use hamburgscleanest\GuzzleAdvancedThrottle\TimeTravel;

class TestClock implements Clock
{
    use TimeTravel;

    private DateTimeImmutable $_now;

    public function __construct(DateTimeImmutable|null $now = null)
    {
        $this->_now = $now ?? new DateTimeImmutable();
    }

    public static function create(DateTimeImmutable|null $now = null): self
    {
        return new self($now);
    }

    public static function fromTimestamp(int $timestamp): self
    {
        return new self((new DateTimeImmutable())->setTimestamp($timestamp));
    }

    public function now(): DateTimeImmutable
    {
        return $this->_now;
    }
}
