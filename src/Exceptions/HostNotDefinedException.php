<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Exceptions;

class HostNotDefinedException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('At least a host has to be defined.');
    }
}
