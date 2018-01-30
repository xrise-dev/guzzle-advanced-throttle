<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateTime;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestInfo;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestInfoTests
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class RequestInfoTest extends TestCase
{

    /** @test
     */
    public function can_be_created_statically()
    {
        $timestamp = (new DateTime())->getTimestamp();
        $requestCount = 15;
        $remainingSeconds = 60;
        $requestInfo = RequestInfo::create($requestCount, $timestamp, $remainingSeconds);

        $this->assertEquals($requestCount, $requestInfo->requestCount);
        $this->assertEquals($remainingSeconds, $requestInfo->remainingSeconds);
        $this->assertEquals($timestamp, $requestInfo->expiresAt->getTimestamp());
    }
}