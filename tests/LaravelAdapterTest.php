<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateTime;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\LaravelAdapter;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheConfigNotSetException;
use Illuminate\Config\Repository;
use PHPUnit\Framework\TestCase;

/**
 * Class LaravelAdapterTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class LaravelAdapterTest extends TestCase
{

    /** @test */
    public function throws_an_exception_when_config_is_not_set()
    {
        $this->expectException(LaravelCacheConfigNotSetException::class);

        new LaravelAdapter();
    }

    /** @test
     */
    public function stores_and_retrieves_data()
    {
        $host = 'test';
        $key = 'my_key';
        $requestCount = 12;
        $expiresAt = new DateTime();
        $remainingSeconds = 120;

        $laravelAdapter = new LaravelAdapter($this->_getConfig());
        $laravelAdapter->save($host, $key, $requestCount, $expiresAt, $remainingSeconds);

        $requestInfo = $laravelAdapter->get($host, $key);
        $this->assertNotNull($requestInfo);
        $this->assertEquals($requestInfo->remainingSeconds, $remainingSeconds);
        $this->assertEquals($requestInfo->requestCount, $requestCount);
        $this->assertEquals($requestInfo->expiresAt->getTimestamp(), $expiresAt->getTimestamp());
    }

    /**
     * @return Repository
     */
    private function _getConfig() : Repository
    {
        return new Repository(require 'config/app.php');
    }

    /** @test
     * @throws \Exception
     */
    public function stores_and_retrieves_response()
    {
        $request = new Request('GET', 'www.test.de');
        $response = new Response(200, [], null, '1337');

        $arrayAdapter = new LaravelAdapter($this->_getConfig());
        $arrayAdapter->saveResponse($request, $response);

        $storedResponse = $arrayAdapter->getResponse($request);

        $this->assertEquals($response, $storedResponse);
    }

    /** @test
     * @throws \Exception
     */
    public function stored_value_gets_invalidated_when_expired()
    {
        $request = new Request('GET', 'www.test.com');
        $response = new Response(200, [], null, '1337');

        $config = $this->_getConfig();
        $config->set('cache.ttl', 0);
        $laravelAdapter = new LaravelAdapter($config);
        $laravelAdapter->saveResponse($request, $response);

        $storedResponse = $laravelAdapter->getResponse($request);

        $this->assertNull($storedResponse);
    }
}