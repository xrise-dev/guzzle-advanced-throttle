<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Helpers;

use Psr\Http\Message\RequestInterface;

/**
 * Class RequestHelper
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Helpers
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

    /**
     * @param RequestInterface $request
     * @return string
     */
    public static function getStorageKey(RequestInterface $request) : string
    {
        $method = $request->getMethod();
        if ($method !== 'GET')
        {
            return self::_getMethodAndParams($method, $request->getBody()->getContents());
        }

        return self::_getMethodAndParams($method, $request->getUri()->getQuery());
    }

    /**
     * @param string $method
     * @param string $params
     * @return string
     */
    private static function _getMethodAndParams(string $method, string $params) : string
    {
        return $method . '_' . $params;
    }
}