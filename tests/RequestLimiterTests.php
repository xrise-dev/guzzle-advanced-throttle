<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimiter;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestLimiterTests
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class RequestLimiterTests extends TestCase
{

    /** @test
     * @throws \Exception
     */
    public function can_be_created_statically()
    {
        $requestLimiter = RequestLimiter::create('www.test.com');

        $this->assertInstanceOf(RequestLimiter::class, $requestLimiter);
    }

    /** @test
     * @throws \Exception
     */
    public function can_be_created_from_rule()
    {
        $requestLimiter = RequestLimiter::createFromRule(['host' => 'www.test.com']);

        $this->assertInstanceOf(RequestLimiter::class, $requestLimiter);
    }

    /** @test
     * @throws \Exception
     */
    public function can_request_is_correct()
    {
        $requestLimiter = RequestLimiter::create('www.test.com', 1);

        $this->assertTrue($requestLimiter->canRequest());
        $this->assertFalse($requestLimiter->canRequest());
    }

    /** @test
     * @throws \Exception
     */
    public function remaining_seconds_are_correct()
    {
        $requestLimiter = RequestLimiter::create('www.test.com', 20, 30);

        $this->assertNull($requestLimiter->getRemainingSeconds());
        $requestLimiter->canRequest();
        $this->assertEquals(30, $requestLimiter->getRemainingSeconds());
    }

    /** @test
     * @throws \Exception
     */
    public function current_request_count_is_correct()
    {
        $requestLimiter = RequestLimiter::create('www.test.com', 1);

        $this->assertEquals(0, $requestLimiter->getCurrentRequestCount());
        $requestLimiter->canRequest();
        $this->assertEquals(1, $requestLimiter->getCurrentRequestCount());
        $requestLimiter->canRequest();
        $this->assertEquals(1, $requestLimiter->getCurrentRequestCount());
    }
}