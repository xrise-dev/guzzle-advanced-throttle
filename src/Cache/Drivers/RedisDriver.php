<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers;

use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\RedisDatabaseNotSetException;
use Illuminate\Redis\RedisManager;

class RedisDriver extends LaravelDriver
{
    protected function _setContainer(): void
    {
        if (!isset($this->_options['database'])) {
            throw new RedisDatabaseNotSetException();
        }

        $this->_container['redis'] = new RedisManager(null, 'predis', ['default' => $this->_options['database']]);
    }
}
