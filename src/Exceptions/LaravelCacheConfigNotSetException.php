<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

class LaravelCacheConfigNotSetException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Laravel was enabled as the cache adapter but no config was found.');
    }
}
