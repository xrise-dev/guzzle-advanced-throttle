<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

class RedisDatabaseNotSetException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Please set a database connection for redis.');
    }
}
