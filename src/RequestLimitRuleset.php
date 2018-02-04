<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\ArrayAdapter;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\LaravelAdapter;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Interfaces\StorageInterface;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownStorageAdapterException;

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
    private $_rules;

    /** @var StorageInterface */
    private $_storage;

    /**
     * RequestLimitRuleset constructor.
     * @param array $rules
     * @param string|null $storageAdapter
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownStorageAdapterException
     */
    public function __construct(array $rules, string $storageAdapter = 'array')
    {
        $this->_rules = $rules;
        $this->_setStorageAdapter($storageAdapter);
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
        $this->_storage = new $storageAdapterClass;
    }

    /**
     * @param array $rules
     * @param string $storageAdapter
     * @return static
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownStorageAdapterException
     */
    public static function create(array $rules, string $storageAdapter = 'array')
    {
        return new static($rules, $storageAdapter);
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