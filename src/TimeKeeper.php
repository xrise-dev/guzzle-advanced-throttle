<?php


namespace hamburgscleanest\GuzzleAdvancedThrottle;

use DateInterval;
use DateTime;


/**
 * Class TimeKeeper
 * @package hamburgscleanest\GuzzleAdvancedThrottle
 */
class TimeKeeper
{

    /** @var DateInterval */
    private $_expirationInterval;

    /** @var DateTime */
    private $_expiresAt;

    /**
     * TimeKeeper constructor.
     * @param DateInterval $interval
     */
    public function __construct(DateInterval $interval)
    {
        $this->_expirationInterval = $interval;
    }

    /**
     * @param DateInterval $interval
     * @return TimeKeeper
     */
    public static function create(DateInterval $interval) : self
    {
        return new static($interval);
    }

    /**
     * @return DateTime
     */
    public function getExpiration() : DateTime
    {
        return $this->_expiresAt;
    }

    /**
     * @param DateTime $expiresAt
     * @return TimeKeeper
     */
    public function setExpiration(DateTime $expiresAt) : self
    {
        $this->_expiresAt = $expiresAt;

        return $this;
    }

    /**
     * @return DateInterval
     */
    public function getRemainingTime() : ?DateInterval
    {
        if ($this->isExpired())
        {
            $this->reset();

            return $this->_expirationInterval;
        }

        $dateDiff = $this->_expiresAt->diff(new DateTime());

        return $dateDiff !== false ? $dateDiff : null;
    }

    /**
     * @return bool
     */
    public function isExpired() : bool
    {
        return $this->_expiresAt !== null && $this->_expiresAt <= new DateTime();
    }

    /**
     *  Reset the request timer.
     */
    public function reset() : void
    {
        $this->_expiresAt = null;
    }

    /**
     * Initialize the expiration date for the request timer.
     */
    public function start() : void
    {
        $this->_expiresAt = (new DateTime())->add($this->_expirationInterval);
    }
}