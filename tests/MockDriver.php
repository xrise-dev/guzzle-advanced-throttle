<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers\LaravelDriver;

class MockDriver extends LaravelDriver
{
    protected function _setContainer(): void
    {
        $this->_container['mock'] = 'test';
    }
}
