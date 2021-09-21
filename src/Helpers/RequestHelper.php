<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Helpers;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Utils;
use Psr\Http\Message\RequestInterface;

class RequestHelper
{
    public static function getHostAndPath(RequestInterface $request): array
    {
        $uri = $request->getUri();

        return [
            $uri->getScheme() . '://' . $uri->getHost(),
            $uri->getPath()
        ];
    }

    public static function getStorageKey(RequestInterface $request): string
    {
        $method = $request->getMethod();
        if ($method !== 'GET') {
            $contentType = $request->getHeader('Content-Type')[0] ?? null;
            $params = $request->getBody()->getContents();

            return self::_getMethodAndParams(
                $method,
                $contentType === 'application/json' ? self::_decodeJSON($params) : $params
            );
        }

        return self::_getMethodAndParams($method, $request->getUri()->getQuery());
    }

    private static function _getMethodAndParams(string $method, string $params): string
    {
        return $method . '_' . self::_sortParams($params);
    }

    private static function _sortParams(string $params): string
    {
        $paramArray = \explode('&', $params);
        \sort($paramArray);

        return \implode('&', $paramArray);
    }

    private static function _decodeJSON(string $json): string
    {
        if (empty($json)) {
            return '';
        }

        return \http_build_query(Utils::jsonDecode($json, true), '', '&');
    }

    public static function getHostFromRequestAndOptions(RequestInterface $request, array $options = []): string
    {
        $requestUri = $request->getUri();

        if (Uri::isAbsolute($requestUri)) {
            return (string) $requestUri;
        }

        if (isset($options['base_uri'])) {
            return $options['base_uri'] .
                UrlHelper::removeTrailingSlash(
                    UrlHelper::prependSlash($requestUri)
                );
        }

        return (string) $requestUri;
    }
}
