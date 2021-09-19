<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle;

use DateInterval;
use DateTimeImmutable;
use hamburgscleanest\GuzzleAdvancedThrottle\Interfaces\Clock;

class SystemClock implements Clock
{
    private DateTimeImmutable $_now;

    public function __construct(DateTimeImmutable|null $now = null)
    {
        $this->_now = $now ?? new DateTimeImmutable();
    }

    public static function create(DateTimeImmutable|null $now = null): self
    {
        return new SystemClock($now);
    }

    public function now(): DateTimeImmutable
    {
        return $this->_now;
    }

    public static function fromTimestamp(int $timestamp): self
    {
        return new self((new DateTimeImmutable())->setTimestamp($timestamp));
    }

    private function _tick(int $value, string $unit = 'S'): self
    {
        $this->_now = $this->_now->add(new DateInterval('PT' . $value . $unit));

        return $this;
    }

    public function advanceSeconds(int $seconds): self
    {
        return $this->_tick($seconds, 'S');
    }

    public function advanceMinutes(int $minutes): self
    {
        return $this->_tick($minutes, 'M');
    }
}
