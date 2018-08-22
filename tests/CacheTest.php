<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Middleware\ThrottleMiddleware;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimitRuleset;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Class CacheTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class CacheTest extends TestCase
{

    /** @test
     */
    public function requests_are_cached() : void
    {
        $host = 'www.test.de';
        $ruleset = new RequestLimitRuleset([
            $host => [
                [
                    'max_requests' => 2
                ]
            ]
        ], 'cache');
        $throttle = new ThrottleMiddleware($ruleset);
        $stack = new MockHandler([new Response(200, [], null, '1'), new Response(200, [], null, '2'), new Response(200, [], null, '3')]);
        $client = new Client(['base_uri' => $host, 'handler' => $throttle->handle()($stack)]);

        $responseOne = $client->request('GET', '/')->getProtocolVersion();
        $responseTwo = $client->request('GET', '/')->getProtocolVersion();
        $responseThree = $client->request('GET', '/')->getProtocolVersion();

        static::assertNotEquals($responseOne, $responseTwo);
        static::assertEquals($responseTwo, $responseThree);
    }

    /** @test
     */
    public function throw_too_many_requests_when_nothing_in_cache() : void
    {
        $host = 'www.test.de';
        $ruleset = new RequestLimitRuleset([
            $host => [
                [
                    'max_requests' => 0
                ]
            ]
        ], 'cache');
        $throttle = new ThrottleMiddleware($ruleset);
        $stack = new MockHandler([new Response()]);
        $client = new Client(['base_uri' => $host, 'handler' => $throttle->handle()($stack)]);

        $this->expectException(TooManyRequestsHttpException::class);
        $client->request('GET', '/');
    }

    /** @test
     */
    public function order_of_parameters_is_irrelevant() : void
    {
        $host = 'www.test.de';
        $ruleset = new RequestLimitRuleset([
            $host => [
                [
                    'max_requests' => 1
                ]
            ]
        ], 'cache');
        $throttle = new ThrottleMiddleware($ruleset);
        $stack = new MockHandler([new Response(200, [], null, '1'), new Response(200, [], null, '2'), new Response(200, [], null, '3')]);
        $client = new Client(['base_uri' => $host, 'handler' => $throttle->handle()($stack)]);

        $responses = [];
        $responses[] = $client->request('GET', '?a=1&b=2&c=3')->getProtocolVersion();
        $responses[] = $client->request('GET', '?b=2&a=1&c=3')->getProtocolVersion();
        $responses[] = $client->request('GET', '?c=3&b=2&a=1')->getProtocolVersion();

        static::assertEquals(['1', '1', '1'], $responses);
    }
}