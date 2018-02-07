<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle;

use GuzzleHttp\Promise\PromiseInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\ArrayAdapter;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\LaravelAdapter;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\CacheStrategy;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Strategies\Cache;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Strategies\ForceCache;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Strategies\NoCache;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownCacheStrategyException;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownStorageAdapterException;
use Illuminate\Config\Repository;
use Psr\Http\Message\RequestInterface;

/**
 * Class RequestLimitRuleset
 * @package hamburgscleanest\GuzzleAdvancedThrottle
 */
class RequestLimitRuleset
{

    /** @var array */
    private const STORAGE_MAP = [
        'array'   => ArrayAdapter::class,
        'laravel' => LaravelAdapter::class
    ];

    /** @var array */
    private const CACHE_STRATEGIES = [
        'no-cache'    => NoCache::class,
        'cache'       => Cache::class,
        'force-cache' => ForceCache::class
    ];

    /** @var array */
    private $_rules;

    /** @var StorageInterface */
    private $_storage;

    /** @var CacheStrategy */
    private $_cacheStrategy;

    /** @var Repository */
    private $_config;

    /**
     * RequestLimitRuleset constructor.
     * @param array $rules
     * @param string $cacheStrategy
     * @param string|null $storageAdapter
     * @param Repository|null $config
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownCacheStrategyException
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownStorageAdapterException
     */
    public function __construct(array $rules, string $cacheStrategy = 'no-cache', string $storageAdapter = 'array', Repository $config = null)
    {
        $this->_rules = $rules;
        $this->_config = $config;
        $this->_setStorageAdapter($storageAdapter);
        $this->_setCacheStrategy($cacheStrategy);
    }

    /**
     * @param string $adapterName
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownStorageAdapterException
     */
    private function _setStorageAdapter(string $adapterName) : void
    {
        if (!isset(self::STORAGE_MAP[$adapterName]))
        {
            throw new UnknownStorageAdapterException($adapterName, self::STORAGE_MAP);
        }

        $storageAdapterClass = self::STORAGE_MAP[$adapterName];
        $this->_storage = new $storageAdapterClass($this->_config);
    }

    /**
     * @param string $cacheStrategy
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownCacheStrategyException
     */
    private function _setCacheStrategy(string $cacheStrategy) : void
    {
        if (!isset(self::CACHE_STRATEGIES[$cacheStrategy]))
        {
            throw new UnknownCacheStrategyException($cacheStrategy, self::CACHE_STRATEGIES);
        }

        $cacheStrategyClass = self::CACHE_STRATEGIES[$cacheStrategy];
        $this->_cacheStrategy = new $cacheStrategyClass($this->_storage);
    }

    /**
     * @param array $rules
     * @param string $cacheStrategy
     * @param string $storageAdapter
     * @return static
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownStorageAdapterException
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownCacheStrategyException
     */
    public static function create(array $rules, string $cacheStrategy = 'no-cache', string $storageAdapter = 'array')
    {
        return new static($rules, $cacheStrategy, $storageAdapter);
    }

    /**
     * @param RequestInterface $request
     * @param callable $handler
     * @return PromiseInterface
     */
    public function cache(RequestInterface $request, callable $handler) : PromiseInterface
    {
        return $this->_cacheStrategy->request($request, $handler);
    }

    /**
     * @return RequestLimitGroup
     * @throws \Exception
     */
    public function getRequestLimitGroup() : RequestLimitGroup
    {
        $requestLimitGroup = new RequestLimitGroup();
        foreach ($this->_rules as $rule)
        {
            $requestLimitGroup->addRequestLimiter(RequestLimiter::createFromRule($rule, $this->_storage));
        }

        return $requestLimitGroup;
    }
}