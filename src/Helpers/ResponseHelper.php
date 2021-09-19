<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Helpers;

use Psr\Http\Message\ResponseInterface;

class ResponseHelper
{
    /**
     * Did the request return a 4xx or 5xx status code?
     * Note: Also handles 3xx redirect codes as errors atm.
     */
    public static function hasErrorStatusCode(ResponseInterface $response): bool
    {
        return $response->getStatusCode() > 299;
    }
}
