<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Middleware\ThrottleMiddleware;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimitRuleset;
use hamburgscleanest\GuzzleAdvancedThrottle\SystemClock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

use Mockery as m;

class ThrottleMiddlewareTest extends TestCase
{
    /** @test */
    public function requests_are_limited(): void
    {
        $host = 'www.test.de';
        $ruleset = new RequestLimitRuleset([
            $host => [
                [
                    'max_requests' => 1
                ]
            ]
        ]);
        $throttle = new ThrottleMiddleware($ruleset);
        $stack = new MockHandler([new Response(200), new Response(200)]);
        $client = new Client(['base_uri' => $host, 'handler' => $throttle->handle()($stack)]);

        $response = $client->request('GET', '/');
        static::assertEquals(200, $response->getStatusCode());

        $this->expectException(TooManyRequestsHttpException::class);
        $client->request('GET', '/');
    }

    /** @test */
    public function requests_are_limited_when_invoked(): void
    {
        $host = 'www.test.de';
        $ruleset = new RequestLimitRuleset([
            $host => [
                [
                    'max_requests' => 1
                ]
            ]
        ]);
        $throttle = new ThrottleMiddleware($ruleset);
        $stack = new MockHandler([new Response(200), new Response(200)]);
        $client = new Client(['base_uri' => $host, 'handler' => $throttle()($stack)]);

        $response = $client->request('GET', '/');
        static::assertEquals(200, $response->getStatusCode());

        $this->expectException(TooManyRequestsHttpException::class);
        $client->request('GET', '/');
    }

    /** @test */
    public function wildcards_are_matched(): void
    {
        $ruleset = new RequestLimitRuleset([
            'www.{subdomain}.test.com' => [
                [
                    'max_requests' => 1
                ]
            ]
        ]);
        $throttle = new ThrottleMiddleware($ruleset);
        $stack = new MockHandler([new Response(200), new Response(200)]);
        $client = new Client(['base_uri' => 'www.en.test.com', 'handler' => $throttle->handle()($stack)]);

        $response = $client->request('GET', '/');

        static::assertEquals(200, $response->getStatusCode());
        $this->expectException(TooManyRequestsHttpException::class);

        $client->request('GET', '/');
    }

    /** @test */
    public function multiple_limits_are_handled_correctly(): void
    {
        $testClock = new TestClock(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2021-09-20 12:00:00'));

        /** @var \Mockery\MockInterface */
        $mockClock = m::mock('overload:' . SystemClock::class);
        $mockClock->shouldReceive('create')->andReturn($testClock);
        $mockClock->shouldReceive('fromTimestamp')->andReturn($testClock);

        $host = 'www.test.de';
        $ruleset = new RequestLimitRuleset([
            $host => [
                [
                    'max_requests' => 1,
                    'request_interval' => 1
                ],
                [
                    'max_requests' => 2,
                    'request_interval' => 2
                ]
            ]
        ]);
        $throttle = new ThrottleMiddleware($ruleset);
        $stack = new MockHandler(
            [
                new Response(200),
                new Response(200),
                new Response(200),
                new Response(200),
            ]
        );
        $client = new Client(['base_uri' => $host, 'handler' => $throttle->handle()($stack)]);

        // 1st rule -> 1 in 1 min

        // 1st req -> ok
        $response = $client->request('GET', '/');
        static::assertEquals(200, $response->getStatusCode());

        // 2nd req -> fail
        $this->expectException(TooManyRequestsHttpException::class);
        $client->request('GET', '/');

        // -----------------------------------
        $testClock->advanceMinutes(1);

        // 2nd rule 2 in 2 min

        // 1st req -> ok (2 in total)
        $response = $client->request('GET', '/');
        static::assertEquals(200, $response->getStatusCode());

        // 2nd req -> fail (3rd in 2 min)
        $this->expectException(TooManyRequestsHttpException::class);
        $client->request('GET', '/');

        // ---------------------------------------
        $testClock->advanceMinutes(1);

        // should be okay again after 3 min

        $response = $client->request('GET', '/');
        static::assertEquals(200, $response->getStatusCode());
    }
}
