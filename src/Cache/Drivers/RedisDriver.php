<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers;

use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\RedisDatabaseNotSetException;

/**
 * Class RedisDriver
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers
 */
class RedisDriver extends LaravelDriver
{

    protected function _setContainer() : void
    {
        if (!isset($this->_options['database']))
        {
            throw new RedisDatabaseNotSetException();
        }

        $this->_container['database']['redis'] = $this->_options['database'];
    }
}