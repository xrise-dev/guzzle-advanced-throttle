<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers;

use Illuminate\Container\Container;


/**
 * Class LaravelDriver
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers
 */
abstract class LaravelDriver
{

    /** @var Container */
    protected $_container;
    /** @var string */
    protected $_driver;
    /** @var array */
    protected $_options;

    public function __construct(string $driver, array $options = [])
    {
        $this->_container = new Container();
        $this->_driver = $driver;
        $this->_options = $options;
    }

    protected abstract function _setContainer() : void;

    private function _setConfig() : void
    {
        $this->_container['config'] = [
            'cache.default'                  => $this->_driver,
            'cache.stores.' . $this->_driver => ['driver' => $this->_driver] + $this->_options
        ];
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    protected function _setStoreValue(string $key, $value)
    {
        $this->_container['config']['cache']['stores'][$this->_driver][$key] = $value;
    }

    /**
     * @return Container
     */
    public function getContainer() : Container
    {
        $this->_setConfig();
        $this->_setContainer();

        return $this->_container;
    }
}