<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use GuzzleHttp\Psr7\Request;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\ArrayAdapter;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimiter;
use PHPUnit\Framework\TestCase;

class RequestLimiterTest extends TestCase
{
    /** @test */
    public function can_be_created_statically(): void
    {
        $requestLimiter = RequestLimiter::create('www.test.com');

        static::assertInstanceOf(RequestLimiter::class, $requestLimiter);
    }

    /** @test */
    public function can_be_created_from_rule(): void
    {
        $requestLimiter = RequestLimiter::createFromRule('www.test.com', []);

        static::assertInstanceOf(RequestLimiter::class, $requestLimiter);
    }

    /** @test */
    public function can_request_is_correct(): void
    {
        $host = 'http://www.test.com';
        $requestLimiter = RequestLimiter::create($host, 1);
        $request = new Request('GET', $host . '/check');

        static::assertTrue($requestLimiter->canRequest($request), 'first request is okay');
        static::assertFalse($requestLimiter->canRequest($request), 'second request is blocked');

        $otherRequest = new Request('GET', 'http://www.check.com');
        static::assertTrue($requestLimiter->canRequest($otherRequest), 'other request is okay');
    }

    /** @test */
    public function does_respect_base_uri(): void
    {
        $host = 'http://www.test.com/api';
        $requestLimiter = RequestLimiter::create($host, 1);
        $request = new Request('GET', $host . '/check');

        static::assertTrue($requestLimiter->canRequest($request));
        static::assertFalse($requestLimiter->canRequest($request));

        $otherRequest = new Request('GET', 'http://www.check.com/api');
        static::assertTrue($requestLimiter->canRequest($otherRequest));
    }

    /** @test */
    public function remaining_seconds_are_correct(): void
    {
        $host = 'http://www.test.com';
        $requestLimiter = RequestLimiter::create($host, 20, 30);

        $requestLimiter->canRequest(new Request('GET', $host . '/check'));
        static::assertEquals(30, $requestLimiter->getRemainingSeconds());
    }

    /** @test */
    public function current_request_count_is_correct(): void
    {
        $host = 'http://www.test.com';
        $requestLimiter = RequestLimiter::create($host, 1);
        $request = new Request('GET', $host . '/check');

        static::assertEquals(0, $requestLimiter->getCurrentRequestCount());
        $requestLimiter->canRequest($request);
        static::assertEquals(1, $requestLimiter->getCurrentRequestCount());
        $requestLimiter->canRequest($request);
        static::assertEquals(2, $requestLimiter->getCurrentRequestCount());
    }

    /** @test */
    public function current_request_count_is_correct_when_expired(): void
    {
        $host = 'http://www.test.com';
        $requestLimiter = RequestLimiter::create($host, 1, 0);
        $requestLimiter->canRequest(new Request('GET', $host . '/check'));

        static::assertEquals(0, $requestLimiter->getCurrentRequestCount());
    }

    /** @test */
    public function restores_state(): void
    {
        $host = 'http://www.test.com';

        $storage = new ArrayAdapter();
        $maxRequests = 15;
        $requestIntervalSeconds = 120;

        $requestLimiterOne = RequestLimiter::create($host, $maxRequests, $requestIntervalSeconds, $storage);
        $requestLimiterOne->canRequest(new Request('GET', $host . '/check'));
        $requestLimiterTwo = RequestLimiter::create($host, $maxRequests, $requestIntervalSeconds, $storage);

        static::assertEquals($requestLimiterOne->getRemainingSeconds(), $requestLimiterTwo->getRemainingSeconds());
        static::assertEquals($requestLimiterOne->getCurrentRequestCount(), $requestLimiterTwo->getCurrentRequestCount());
    }

    /** @test */
    public function matches_host_correctly(): void
    {
        $host = 'http://www.test.com';

        $requestLimiter = RequestLimiter::create($host);

        static::assertTrue($requestLimiter->matches($host));
        static::assertFalse($requestLimiter->matches('http://www.check.com'));
    }
}
