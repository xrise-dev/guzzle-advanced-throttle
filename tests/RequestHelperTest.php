<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Utils;
use hamburgscleanest\GuzzleAdvancedThrottle\Helpers\RequestHelper;
use PHPUnit\Framework\TestCase;

class RequestHelperTest extends TestCase
{

    /** @test */
    public function gets_correct_host_and_path(): void
    {
        $url = 'https://www.test.com/path';
        [$host, $path] = RequestHelper::getHostAndPath(new Request('GET', $url));

        static::assertEquals($url, $host . $path);
    }

    /** @test */
    public function gets_correct_storage_key_for_get(): void
    {
        $query = 'test=someVal';

        static::assertEquals('GET_' . $query, RequestHelper::getStorageKey(new Request('GET', 'https://www.test.com/path?' . $query)));
    }

    /** @test */
    public function gets_correct_storage_key_for_post_form_params(): void
    {
        $body = ['test' => 'someVal'];

        static::assertEquals(
            'POST_test=someVal',
            RequestHelper::getStorageKey(
                new Request(
                    'POST',
                    'https://www.test.com/path',
                    [],
                    http_build_query($body, '', '&')
                )
            )
        );
    }

    /** @test */
    public function gets_correct_storage_key_for_post_json(): void
    {
        $body = ['test' => 'someVal'];

        static::assertEquals(
            'POST_test=someVal',
            RequestHelper::getStorageKey(
                new Request(
                    'POST',
                    'https://www.test.com/path',
                    ['Content-Type' => 'application/json'],
                    Utils::jsonEncode($body)
                )
            )
        );
    }

    /** @test */
    public function gets_correct_host_from_request_and_options(): void
    {
        static::assertEquals(
            'www.test.de/api',
            RequestHelper::getHostFromRequestAndOptions(
                new Request('get', 'www.test.de/api')
            ),
            'absolute path'
        );

        static::assertEquals(
            'https://test.de/api',
            RequestHelper::getHostFromRequestAndOptions(
                new Request('get', 'https://test.de/api')
            ),
            'absolute path with schema'
        );

        static::assertEquals(
            'www.test.de/api',
            RequestHelper::getHostFromRequestAndOptions(
                new Request('get', 'api'),
                ['base_uri' => new Uri('www.test.de')]
            ),
            'base_uri set + relative path'
        );

        static::assertEquals(
            'https://test.de/api',
            RequestHelper::getHostFromRequestAndOptions(
                new Request('get', 'https://test.de/api'),
                ['base_uri' => new Uri('https://test.de')]
            ),
            'base_uri set + absolute path'
        );
    }
}
