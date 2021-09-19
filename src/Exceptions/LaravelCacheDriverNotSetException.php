<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

class LaravelCacheDriverNotSetException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Laravel was enabled as the cache adapter but no driver was configured.');
    }
}
