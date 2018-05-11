<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateTime;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\ArrayAdapter;
use Illuminate\Config\Repository;
use PHPUnit\Framework\TestCase;

/**
 * Class ArrayAdapterTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class ArrayAdapterTest extends TestCase
{

    /** @test
     */
    public function stores_and_retrieves_data() : void
    {
        $host = 'test';
        $key = 'my_key';
        $requestCount = 12;
        $expiresAt = new DateTime();
        $remainingSeconds = 120;

        $arrayAdapter = new ArrayAdapter();
        $arrayAdapter->save($host, $key, $requestCount, $expiresAt, $remainingSeconds);

        $requestInfo = $arrayAdapter->get($host, $key);
        static::assertNotNull($requestInfo);
        static::assertEquals($requestInfo->remainingSeconds, $remainingSeconds);
        static::assertEquals($requestInfo->requestCount, $requestCount);
        static::assertEquals($requestInfo->expiresAt->getTimestamp(), $expiresAt->getTimestamp());
    }

    /** @test
     * @throws \Exception
     */
    public function stores_and_retrieves_response() : void
    {
        $request = new Request('GET', 'www.test.de');
        $response = new Response(200, [], null, '1337');

        $arrayAdapter = new ArrayAdapter();
        $arrayAdapter->saveResponse($request, $response);

        $storedResponse = $arrayAdapter->getResponse($request);

        static::assertEquals($response, $storedResponse);
    }

    /** @test
     * @throws \Exception
     */
    public function stored_value_gets_invalidated_when_expired() : void
    {
        $request = new Request('GET', 'www.test.com');
        $response = new Response(200, [], null, '1337');

        $arrayAdapter = new ArrayAdapter(new Repository(['cache' => ['ttl' => 0]]));
        $arrayAdapter->saveResponse($request, $response);

        static::assertNull($arrayAdapter->getResponse($request));
    }
}