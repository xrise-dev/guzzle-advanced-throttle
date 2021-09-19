<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

class MemcachedServersNotSetException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Please set the servers for memcached.');
    }
}
