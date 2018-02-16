<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use GuzzleHttp\Psr7\Request;
use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers\RequestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestHelperTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class RequestHelperTest extends TestCase
{

    /** @test
     */
    public function gets_correct_host_and_path() : void
    {
        $url = 'https://www.test.com/path';
        [$host, $path] = RequestHelper::getHostAndPath(new Request('GET', $url));

        $this->assertEquals($url, $host . $path);
    }

    /** @test
     */
    public function gets_correct_storage_key_for_get() : void
    {
        $query = 'test=someVal';

        $this->assertEquals('GET_' . $query, RequestHelper::getStorageKey(new Request('GET', 'https://www.test.com/path?' . $query)));
    }

    /** @test
     */
    public function gets_correct_storage_key_for_post() : void
    {
        $body = 'test=someVal';

        $this->assertEquals('POST_' . $body, RequestHelper::getStorageKey(new Request('POST', 'https://www.test.com/path', [], $body)));
    }

}