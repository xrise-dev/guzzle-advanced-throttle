<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\UnknownStorageAdapterException;
use hamburgscleanest\GuzzleAdvancedThrottle\RequestLimitRuleset;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestLimitRulesetTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class RequestLimitRulesetTest extends TestCase
{

    /** @test */
    public function can_be_created_statically()
    {
        $requestLimitRuleset = RequestLimitRuleset::create([]);

        $this->assertInstanceOf(RequestLimitRuleset::class, $requestLimitRuleset);
    }

    /** @test */
    public function throws_unknown_storage_adapter_exception()
    {
        $this->expectException(UnknownStorageAdapterException::class);

        RequestLimitRuleset::create([], 'garbage');
    }

    /** @test
     * @throws \Exception
     */
    public function ruleset_contains_the_correct_request_limit_group()
    {
        $interval = 33;

        $requestLimitRuleset = RequestLimitRuleset::create([['host' => 'www.test.de', 'max_requests' => 0, 'request_interval' => $interval]]);
        $requestLimitGroup = $requestLimitRuleset->getRequestLimitGroup();
        $requestLimitGroup->canRequest();

        $this->assertEquals($interval, $requestLimitGroup->getRetryAfter());
    }
}