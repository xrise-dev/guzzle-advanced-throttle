<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\ArrayAdapter;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Strategies\Cache;
use hamburgscleanest\GuzzleAdvancedThrottle\Middleware\ThrottleMiddleware;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimitRuleset;
use PHPUnit\Framework\TestCase;

/**
 * Class CacheTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class CacheTest extends TestCase
{

    /** @test
     * @throws \Exception
     */
    public function requests_are_cached()
    {
        $host = 'www.test.de';
        $ruleset = new RequestLimitRuleset([
            [
                'host'         => $host,
                'max_requests' => 2
            ]
        ]);
        $storage = new ArrayAdapter();
        $throttle = new ThrottleMiddleware($ruleset, new Cache($storage));
        $stack = new MockHandler([new Response(200, [], null, '1'), new Response(200, [], null, '2'), new Response(200, [], null, '3')]);
        $client = new Client(['base_uri' => $host, 'handler' => $throttle->handle()($stack)]);

        $responseOne = $client->request('GET', '/')->getProtocolVersion();
        $responseTwo = $client->request('GET', '/')->getProtocolVersion();
        $responseThree = $client->request('GET', '/')->getProtocolVersion();

        $this->assertNotEquals($responseOne, $responseTwo);
        $this->assertEquals($responseTwo, $responseThree);
    }

}