<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers;

use Psr\Http\Message\RequestInterface;

/**
 * Class RequestHelper
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers
 */
class RequestHelper
{

    /**
     * @param RequestInterface $request
     * @return array
     */
    public static function getHostAndPath(RequestInterface $request) : array
    {
        $uri = $request->getUri();

        return [
            $uri->getScheme() . '://' . $uri->getHost(),
            $uri->getPath()
        ];
    }
}