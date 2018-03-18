<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers;

use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\MemcachedServersNotSetException;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\RedisDatabaseNotSetException;
use Illuminate\Cache\MemcachedConnector;

/**
 * Class MemcachedDriver
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers
 */
class MemcachedDriver extends LaravelDriver
{

    protected function _setContainer() : void
    {
        if (!isset($this->_options['servers']))
        {
            throw new MemcachedServersNotSetException();
        }

        $this->_setStoreValue('servers', $this->_options['servers']);
        $this->_container['memcached.connector'] = new MemcachedConnector();
    }
}