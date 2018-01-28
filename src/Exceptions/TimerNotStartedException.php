<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

/**
 * Class TimerNotStartedException
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Exceptions
 */
class TimerNotStartedException extends \RuntimeException
{

    /**
     * TimerNotStartedException constructor.
     */
    public function __construct()
    {
        parent::__construct('You have not started the timer yet!');
    }
}