<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters;

use DateInterval;
use DateTime;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers\RequestHelper;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;
use Illuminate\Config\Repository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ArrayAdapter
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters
 */
class ArrayAdapter implements StorageInterface
{

    /** @var int */
    private const DEFAULT_TTL = 300;
    /** @var string */
    private const STORAGE_KEY = 'requests';
    /** @var string */
    private const RESPONSE_KEY = 'response';
    /** @var string */
    private const EXPIRATION_KEY = 'expires_at';
    /** @var int Time To Live in minutes */
    private $_ttl = self::DEFAULT_TTL;

    /** @var array */
    private $_storage = [];

    /**
     * StorageInterface constructor.
     * @param Repository|null $config
     */
    public function __construct(?Repository $config = null)
    {
        if ($config === null)
        {
            return;
        }

        $this->_ttl = $config->get('cache.ttl', self::DEFAULT_TTL);
    }

    /**
     * @param string $host
     * @param string $key
     * @param int $requestCount
     * @param \DateTime $expiresAt
     * @param int $remainingSeconds
     */
    public function save(string $host, string $key, int $requestCount, DateTime $expiresAt, int $remainingSeconds) : void
    {
        $this->_storage[$host][$key] = RequestInfo::create($requestCount, $expiresAt->getTimestamp(), $remainingSeconds);
    }

    /**
     * @param string $host
     * @param string $key
     * @return RequestInfo|null
     */
    public function get(string $host, string $key) : ?RequestInfo
    {
        return $this->_storage[$host][$key] ?? null;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws \Exception
     */
    public function saveResponse(RequestInterface $request, ResponseInterface $response) : void
    {
        [$host, $path] = RequestHelper::getHostAndPath($request);

        $this->_storage[self::STORAGE_KEY][$host][$path][RequestHelper::getStorageKey($request)] = [
            self::RESPONSE_KEY   => $response,
            self::EXPIRATION_KEY => (new DateTime())->add(new DateInterval('PT' . $this->_ttl . 'M'))->getTimestamp()
        ];
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null
     */
    public function getResponse(RequestInterface $request) : ?ResponseInterface
    {
        [$host, $path] = RequestHelper::getHostAndPath($request);
        $key = RequestHelper::getStorageKey($request);

        $response = $this->_storage[self::STORAGE_KEY][$host][$path][$key] ?? null;
        if ($response !== null)
        {
            if ($response[self::EXPIRATION_KEY] > \time())
            {
                return $response[self::RESPONSE_KEY];
            }

            $this->_invalidate($host, $path, $key);
        }

        return null;
    }

    /**
     * @param string $host
     * @param string $path
     * @param string $key
     */
    private function _invalidate(string $host, string $path, string $key) : void
    {
        unset($this->_storage[self::STORAGE_KEY][$host][$path][$key]);
    }
}