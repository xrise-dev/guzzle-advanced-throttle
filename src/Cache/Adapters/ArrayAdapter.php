<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters;

use DateInterval;
use DateTime;
use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\CachedResponse;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Config\Repository;

class ArrayAdapter extends BaseAdapter
{
    /** @var string */
    private const RESPONSE_KEY = 'response';
    /** @var string */
    private const EXPIRATION_KEY = 'expires_at';

    private array $_storage = [];

    public function __construct(?Repository $config = null)
    {
        if ($config === null) {
            return;
        }

        $this->_ttl = $config->get('cache.ttl', self::DEFAULT_TTL);
        $this->_allowEmptyValues = $config->get('cache.allow_empty', $this->_allowEmptyValues);
    }

    public function save(string $host, string $key, int $requestCount, DateTime $expiresAt, int $remainingSeconds): void
    {
        $this->_storage[$host][$key] = RequestInfo::create($requestCount, $expiresAt->getTimestamp(), $remainingSeconds);
    }

    public function get(string $host, string $key): ?RequestInfo
    {
        return $this->_storage[$host][$key] ?? null;
    }

    protected function _saveResponse(ResponseInterface $response, string $host, string $path, string $key): void
    {
        $this->_storage[self::STORAGE_KEY][$host][$path][$key] = [
            self::RESPONSE_KEY   => new CachedResponse($response),
            self::EXPIRATION_KEY => (new DateTime())->add(new DateInterval('PT' . $this->_ttl . 'M'))->getTimestamp()
        ];
    }

    protected function _getResponse(string $host, string $path, string $key): ?Response
    {
        $response = $this->_storage[self::STORAGE_KEY][$host][$path][$key] ?? null;

        if ($response !== null) {
            if ($response[self::EXPIRATION_KEY] > \time()) {
                /** @var CachedResponse|null $cachedResponse */
                $cachedResponse = $response[self::RESPONSE_KEY];

                return $cachedResponse ? $cachedResponse->getResponse() : null;
            }

            $this->_invalidate($host, $path, $key);
        }

        return null;
    }

    private function _invalidate(string $host, string $path, string $key): void
    {
        unset($this->_storage[self::STORAGE_KEY][$host][$path][$key]);
    }
}
