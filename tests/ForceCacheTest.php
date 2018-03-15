<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Middleware\ThrottleMiddleware;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimitRuleset;
use PHPUnit\Framework\TestCase;

/**
 * Class ForceCacheTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class ForceCacheTest extends TestCase
{

    /** @test
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requests_are_always_cached() : void
    {
        $host = 'www.test.de';
        $ruleset = new RequestLimitRuleset([
            $host => [
                [
                    'max_requests' => 2
                ]
            ]
        ], 'force-cache');
        $throttle = new ThrottleMiddleware($ruleset);
        $stack = new MockHandler([new Response(200, [], null, '1'), new Response(200, [], null, '2'), new Response(200, [], null, '3')]);
        $client = new Client(['base_uri' => $host, 'handler' => $throttle->handle()($stack)]);

        $responseOne = $client->request('GET', '/')->getProtocolVersion();
        $responseTwo = $client->request('GET', '/')->getProtocolVersion();
        $responseThree = $client->request('GET', '/')->getProtocolVersion();

        $this->assertEquals($responseOne, $responseTwo);
        $this->assertEquals($responseTwo, $responseThree);
    }

}