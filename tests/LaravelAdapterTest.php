<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\LaravelAdapter;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheConfigNotSetException;
use Illuminate\Config\Repository;
use PHPUnit\Framework\TestCase;

class LaravelAdapterTest extends TestCase
{
    /** @test */
    public function throws_an_exception_when_config_is_not_set(): void
    {
        $this->expectException(LaravelCacheConfigNotSetException::class);

        new LaravelAdapter();
    }

    /** @test */
    public function stores_and_retrieves_data(): void
    {
        $host = 'test';
        $key = 'my_key';
        $requestCount = 12;
        $expiresAt = new DateTimeImmutable();
        $remainingSeconds = 120;

        $laravelAdapter = new LaravelAdapter($this->_getConfig());
        $laravelAdapter->save($host, $key, $requestCount, $expiresAt, $remainingSeconds);

        $requestInfo = $laravelAdapter->get($host, $key);
        static::assertNotNull($requestInfo);
        static::assertEquals($requestInfo->remainingSeconds, $remainingSeconds);
        static::assertEquals($requestInfo->requestCount, $requestCount);
        static::assertEquals($requestInfo->expiresAt->getTimestamp(), $expiresAt->getTimestamp());
    }

    private function _getConfig(): Repository
    {
        return new Repository(require 'config/app.php');
    }

    /** @test */
    public function stored_value_gets_invalidated_when_expired(): void
    {
        $request = new Request('GET', 'www.test.com');
        $response = new Response(200, [], null);

        $config = $this->_getConfig();
        $config->set('cache.ttl', 0);
        $laravelAdapter = new LaravelAdapter($config);
        $laravelAdapter->saveResponse($request, $response);

        $storedResponse = $laravelAdapter->getResponse($request);

        static::assertNull($storedResponse);
    }

    /** @test */
    public function does_not_store_empty_values(): void
    {
        $request = new Request('GET', 'www.test.com');
        $nullResponse = new Response(200, [], null);
        $config = $this->_getConfig();
        $config->set('cache.allow_empty', false);

        $laravelAdapter = new LaravelAdapter($config);
        $laravelAdapter->saveResponse($request, $nullResponse);

        static::assertNull($laravelAdapter->getResponse($request));

        $emptyResponse = new Response(200, [], '');

        $laravelAdapter = new LaravelAdapter($config);
        $laravelAdapter->saveResponse($request, $emptyResponse);

        static::assertNull($laravelAdapter->getResponse($request));
    }

    /** @test */
    public function stores_empty_values_when_allowed(): void
    {
        $request = new Request('GET', 'www.test.com');
        $nullResponse = new Response(200, [], null);
        $config = $this->_getConfig();
        $config->set('cache.allow_empty', true);

        $laravelAdapter = new LaravelAdapter($config);
        $laravelAdapter->saveResponse($request, $nullResponse);

        static::assertEmpty((string) $laravelAdapter->getResponse($request)->getBody());

        $emptyResponse = new Response(200, [], '');

        $laravelAdapter = new LaravelAdapter($config);
        $laravelAdapter->saveResponse($request, $emptyResponse);

        static::assertEmpty((string) $laravelAdapter->getResponse($request)->getBody());
    }
}
