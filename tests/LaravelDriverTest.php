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
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
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
}