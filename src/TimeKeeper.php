<?php


namespace hamburgscleanest\GuzzleAdvancedThrottle;

use DateInterval;
use DateTime;

class TimeKeeper
{
    private int $_expirationIntervalSeconds;
    private DateTime|null $_expiresAt = null;

    public function __construct(int $intervalInSeconds)
    {
        $this->_expirationIntervalSeconds = $intervalInSeconds;
    }

    public static function create(int $intervalInSeconds): self
    {
        return new static($intervalInSeconds);
    }

    public function getExpiration(): ?DateTime
    {
        return $this->_expiresAt;
    }

    public function setExpiration(DateTime $expiresAt): self
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

        return $this->_expiresAt <= new DateTime();
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
        $this->_expiresAt = (new DateTime())->add(new DateInterval('PT' . $this->_expirationIntervalSeconds . 'S'));
    }
}
