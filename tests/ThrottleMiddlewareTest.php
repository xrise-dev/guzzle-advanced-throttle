<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Middleware\ThrottleMiddleware;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimitRuleset;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use SlopeIt\ClockMock\ClockMock;


class ThrottleMiddlewareTest extends TestCase
{
    /** @test */
    public function requests_are_limited(): void
    {
        $host = 'www.test.de';
        $ruleset = new RequestLimitRuleset([
            $host => [
                [
                    'max_requests' => 1,
                    'request_interval' => 60
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
                    'max_requests' => 1,
                    'request_interval' => 60
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
                    'max_requests' => 1,
                    'request_interval' => 60
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
        $host = 'www.test.de';
        $ruleset = new RequestLimitRuleset([
            $host => [
                [
                    'max_requests' => 1,
                    'request_interval' => 60
                ],
                [
                    'max_requests' => 3,
                    'request_interval' => 300
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
                new Response(200),
            ]
        );
        $client = new Client(['base_uri' => $host, 'handler' => $throttle->handle()($stack)]);

        // ----------------------------------------------------------------
        ClockMock::freeze(new DateTimeImmutable('2021-09-21 12:00:00'));

        // 1st rule -> 1 in 1 min

        $response = $client->request('GET', '/1');
        static::assertEquals(200, $response->getStatusCode(), '[rule 1] request is okay');

        // 2nd req -> fail (2 in 1 min)
        try {
            $client->request('GET', '/2');
        } catch (TooManyRequestsHttpException $exception) {
            $headers = $exception->getHeaders();
            static::assertArrayHasKey('Retry-After', $headers);
            static::assertEquals(60, $headers['Retry-After']);
        }

        // ----------------------------------------------------------------
        ClockMock::freeze(new DateTimeImmutable('2021-09-21 12:01:00'));

        // 2nd rule 3 in 5 min

        $response = $client->request('GET', '/3');
        static::assertEquals(200, $response->getStatusCode(), '[rule 2] request is okay');

        // 2nd req -> fail (4th in 5 min)
        try {
            $client->request('GET', '/4');
        } catch (TooManyRequestsHttpException $exception) {
            $headers = $exception->getHeaders();
            static::assertArrayHasKey('Retry-After', $headers);
            static::assertEquals(240, $headers['Retry-After']);
        }

        // --------------------------------------------------------------
        ClockMock::freeze(new DateTimeImmutable('2021-09-21 12:10:00'));

        $response = $client->request('GET', '/5');
        static::assertEquals(200, $response->getStatusCode(), 'should be okay again after 10 min');

        ClockMock::reset();
    }
}
