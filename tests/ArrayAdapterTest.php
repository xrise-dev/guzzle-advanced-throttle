<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateTime;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Adapters\ArrayAdapter;
use PHPUnit\Framework\TestCase;

/**
 * Class ArrayAdapterTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class ArrayAdapterTest extends TestCase
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

        $arrayAdapter = new ArrayAdapter();
        $arrayAdapter->save($host, $key, $requestCount, $expiresAt, $remainingSeconds);

        $requestInfo = $arrayAdapter->get($host, $key);
        $this->assertNotNull($requestInfo);
        $this->assertEquals($requestInfo->remainingSeconds, $remainingSeconds);
        $this->assertEquals($requestInfo->requestCount, $requestCount);
        $this->assertEquals($requestInfo->expiresAt->getTimestamp(), $expiresAt->getTimestamp());
    }
}