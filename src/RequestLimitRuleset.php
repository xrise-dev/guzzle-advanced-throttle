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
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\HostNotDefinedException;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownCacheStrategyException;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownStorageAdapterException;
use hamburgscleanest\GuzzleAdvancedThrottle\Helpers\InterfaceHelper;
use Illuminate\Config\Repository;
use Psr\Http\Message\RequestInterface;

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

    private array $_rules;
    private StorageInterface $_storage;
    private CacheStrategy $_cacheStrategy;
    private Repository|null $_config;

    public function __construct(array $rules, string $cacheStrategy = 'no-cache', string $storageAdapter = 'array', Repository $config = null)
    {
        $this->_rules = $rules;
        $this->_config = $config;
        $this->_setStorageAdapter($storageAdapter);
        $this->_setCacheStrategy($cacheStrategy);
    }

    private function _setStorageAdapter(string $adapterName): void
    {
        $adapterClassName = self::STORAGE_MAP[$adapterName] ?? $adapterName;

        if (!InterfaceHelper::implementsInterface($adapterClassName, StorageInterface::class)) {
            throw new UnknownStorageAdapterException($adapterClassName, \array_values(self::STORAGE_MAP));
        }

        $this->_storage = new $adapterClassName($this->_config);
    }

    private function _setCacheStrategy(string $cacheStrategy): void
    {
        $cacheStrategyClassName = self::CACHE_STRATEGIES[$cacheStrategy] ?? $cacheStrategy;

        if (!InterfaceHelper::implementsInterface($cacheStrategyClassName, CacheStrategy::class)) {
            throw new UnknownCacheStrategyException($cacheStrategyClassName, \array_values(self::CACHE_STRATEGIES));
        }

        $this->_cacheStrategy = new $cacheStrategyClassName($this->_storage);
    }

    public static function create(array $rules, string $cacheStrategy = 'no-cache', string $storageAdapter = 'array')
    {
        return new static($rules, $cacheStrategy, $storageAdapter);
    }

    public function cache(RequestInterface $request, callable $handler): PromiseInterface
    {
        return $this->_cacheStrategy->request($request, $handler);
    }

    public function getRequestLimitGroup(): RequestLimitGroup
    {
        $requestLimitGroup = new RequestLimitGroup();
        foreach ($this->_rules as $host => $rules) {
            if (!\is_string($host)) {
                throw new HostNotDefinedException();
            }

            $requestLimitGroup->addRules($host, $rules, $this->_storage);
        }

        return $requestLimitGroup;
    }
}
