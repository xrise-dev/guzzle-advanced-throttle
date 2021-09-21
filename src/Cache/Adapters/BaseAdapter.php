<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters;

use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\Helpers\RequestHelper;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class BaseAdapter implements StorageInterface
{
    /** @var int */
    protected const DEFAULT_TTL = 300;
    /** @var string */
    protected const STORAGE_KEY = 'requests';

    /** Time To Live in minutes */
    protected int $_ttl = self::DEFAULT_TTL;
    /** false -> empty responses won't be cached. */
    protected bool $_allowEmptyValues = true;

    final public function saveResponse(RequestInterface $request, ResponseInterface $response): void
    {
        if (!$this->_allowEmptyValues && $response->getBody()->getSize() === 0) {
            return;
        }

        [$host, $path] = RequestHelper::getHostAndPath($request);

        $this->_saveResponse($response, $host, $path, RequestHelper::getStorageKey($request));
    }

    abstract protected function _saveResponse(ResponseInterface $response, string $host, string $path, string $key): void;

    final public function getResponse(RequestInterface $request): ?ResponseInterface
    {
        [$host, $path] = RequestHelper::getHostAndPath($request);

        return $this->_getResponse($host, $path, RequestHelper::getStorageKey($request));
    }

    abstract protected function _getResponse(string $host, string $path, string $key): ?Response;
}
