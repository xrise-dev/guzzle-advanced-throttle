<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimiter;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimitGroup;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestLimiterGroupTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class RequestLimitGroupTest extends TestCase
{

    /** @test */
    public function can_be_created_statically()
    {
        $requestLimitGroup = RequestLimitGroup::create();

        $this->assertInstanceOf(RequestLimitGroup::class, $requestLimitGroup);
    }

    /** @test
     * @throws \Exception
     */
    public function can_add_request_limiters()
    {
        $requestLimitGroup = RequestLimitGroup::create();
        $requestLimitGroup->addRequestLimiter(new RequestLimiter('www.test'));

        $this->assertEquals(1, $requestLimitGroup->getRequestLimiterCount());
    }

    /** @test
     * @throws \Exception
     */
    public function can_remove_request_limiters()
    {
        $requestLimiter = new RequestLimiter('www.test');

        $requestLimitGroup = RequestLimitGroup::create();
        $requestLimitGroup->addRequestLimiter($requestLimiter);
        $requestLimitGroup->removeRequestLimiter($requestLimiter);

        $this->assertEquals(0, $requestLimitGroup->getRequestLimiterCount());
    }

    /** @test
     * @throws \Exception
     */
    public function can_request_is_correct()
    {
        $interval = 100;
        $requestLimitGroup = RequestLimitGroup::create();
        $requestLimitGroup->addRequestLimiter(new RequestLimiter('www.test', 1, $interval));

        $this->assertTrue($requestLimitGroup->canRequest());
        $this->assertFalse($requestLimitGroup->canRequest());
    }

    /** @test
     * @throws \Exception
     */
    public function retry_seconds_are_correct()
    {
        $interval = 100;
        $requestLimitGroup = RequestLimitGroup::create();

        $this->assertEquals(0, $requestLimitGroup->getRetryAfter());

        $requestLimitGroup->addRequestLimiter(new RequestLimiter('www.test', 0, $interval));
        $requestLimitGroup->canRequest();

        $this->assertEquals($interval, $requestLimitGroup->getRetryAfter());
    }

}