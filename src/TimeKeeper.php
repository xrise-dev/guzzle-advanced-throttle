<?php


namespace hamburgscleanest\GuzzleAdvancedThrottle;

use DateInterval;
use DateTimeImmutable;

class TimeKeeper
{
    private int $_expirationIntervalSeconds;
    private DateTimeImmutable|null $_expiresAt = null;

    public function __construct(int $intervalInSeconds)
    {
        $this->_expirationIntervalSeconds = $intervalInSeconds;
    }

    public static function create(int $intervalInSeconds): self
    {
        return new static($intervalInSeconds);
    }

    public function getExpiration(): ?DateTimeImmutable
    {
        return $this->_expiresAt;
    }

    public function setExpiration(DateTimeImmutable $expiresAt): self
    {
        $this->_expiresAt = $expiresAt;

        return $this;
    }

    public function getRemainingSeconds(): int
    {
        return $this->_expiresAt === null || $this->isExpired() ? $this->_expirationIntervalSeconds : $this->_expiresAt->getTimestamp() - \time();
    }

    public function isExpired(): bool
    {
        if ($this->_expiresAt === null) {
            return false;
        }

        return $this->_expiresAt <= new DateTimeImmutable();
    }

    public function reset(): void
    {
        $this->_expiresAt = null;
    }

    /**
     * Initialize the expiration date for the request timer.
     */
    public function start(): void
    {
        $this->_expiresAt = (new DateTimeImmutable())->add(new DateInterval('PT' . $this->_expirationIntervalSeconds . 'S'));
    }
}
