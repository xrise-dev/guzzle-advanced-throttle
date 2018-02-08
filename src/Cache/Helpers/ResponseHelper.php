<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseHelper
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers
 */
class ResponseHelper
{

    /**
     * Did the request return a 4xx or 5xx status code?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function hasErrorStatusCode(ResponseInterface $response) : bool
    {
        return \in_array(+ \mb_substr($response->getStatusCode(), 0, 1), [4, 5], true);
    }
}