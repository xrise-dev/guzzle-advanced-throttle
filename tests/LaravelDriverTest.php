<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownLaravelDriverException;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimitRuleset;
use Illuminate\Config\Repository;
use PHPUnit\Framework\TestCase;

/**
 * Class LaravelDriverTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class LaravelDriverTest extends TestCase
{

    /** @test
     */
    public function throws_unknown_driver_exception() : void
    {
        $this->expectException(UnknownLaravelDriverException::class);

        new RequestLimitRuleset([
            'www.test.de' => [
                [
                    'max_requests' => 2
                ]
            ]
        ],
            'cache',
            'laravel',
            new Repository([
                'cache' => [
                    'driver' => 'bullshit',
                ]
            ]));
    }

    /** @test
     */
    public function container_is_built_correctly() : void
    {
        $mockDriver = new MockDriver('mock');

        static::assertArraySubset(
            [
                'cache.default'     => 'mock',
                'cache.stores.mock' => [
                    'driver' => 'mock'
                ],
                'cache.prefix'      => 'throttle_cache'
            ],
            $mockDriver->getContainer()->offsetGet('config')
        );
    }
}