<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers\LaravelDriver;
use PHPUnit\Framework\TestCase;

/**
 * Class LaravelDriverTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class LaravelDriverTest extends TestCase
{

    /** @test */
    public function gets_correct_driver_config() : void
    {
        $driver = 'test';
        $options = ['my_option' => true];
        $config = LaravelDriver::getDriverConfig($driver, $options);

        $this->assertEquals(
            ['cache.default' => $driver, 'cache.stores.' . $driver => ['driver' => $driver] + $options],
            $config
        );
    }

}
