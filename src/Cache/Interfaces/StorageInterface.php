<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces;

use DateTime;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;

/**
 * Interface StorageInterface
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces
 */
interface StorageInterface
{

    /**
     * @param string $host
     * @param string $key
     * @param int $requestCount
     * @param DateTime $expiresAt
     * @param int $remainingSeconds
     */
    public function save(string $host, string $key, int $requestCount, DateTime $expiresAt, int $remainingSeconds) : void;

    /**
     * @param string $host
     * @param string $key
     * @return RequestInfo|null
     */
    public function get(string $host, string $key) : ? RequestInfo;
}