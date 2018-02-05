<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateTime;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\LaravelAdapter;
use PHPUnit\Framework\TestCase;

/**
 * Class LaravelAdapterTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class LaravelAdapterTest extends TestCase
{

    /** @test
     */
    public function stores_and_retrieves_data()
    {
        $host = 'test';
        $key = 'my_key';
        $requestCount = 12;
        $expiresAt = new DateTime();
        $remainingSeconds = 120;

        $laravelAdapter = new LaravelAdapter();
        $laravelAdapter->save($host, $key, $requestCount, $expiresAt, $remainingSeconds);

        $requestInfo = $laravelAdapter->get($host, $key);
        $this->assertNotNull($requestInfo);
        $this->assertEquals($requestInfo->remainingSeconds, $remainingSeconds);
        $this->assertEquals($requestInfo->requestCount, $requestCount);
        $this->assertEquals($requestInfo->expiresAt->getTimestamp(), $expiresAt->getTimestamp());
    }

    /** @test
     * @throws \Exception
     */
    public function stores_and_retrieves_response()
    {
        $request = new Request('GET', 'www.test.de');
        $response = new Response(200, [], null, '1337');

        $arrayAdapter = new LaravelAdapter();
        $arrayAdapter->saveResponse($request, $response);

        $storedResponse = $arrayAdapter->getResponse($request);

        $this->assertEquals($response, $storedResponse);
    }
}