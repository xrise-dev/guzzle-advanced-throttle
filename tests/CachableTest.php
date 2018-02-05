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
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Class CachableTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class CachableTest extends TestCase
{

    /** @test
     * @throws \Exception
     */
    public function dont_cache_error_responses()
    {
        $host = 'www.test.de';
        $ruleset = new RequestLimitRuleset([
            [
                'host'         => $host,
                'max_requests' => 1
            ]
        ]);
        $storage = new ArrayAdapter();
        $throttle = new ThrottleMiddleware($ruleset, new Cache($storage));
        $stack = new MockHandler([new Response(500), new Response()]);
        $client = new Client(['base_uri' => $host, 'handler' => $throttle->handle()($stack)]);

        $client->request('GET', '/');

        $this->expectException(TooManyRequestsHttpException::class);

        $client->request('GET', '/');
    }

}