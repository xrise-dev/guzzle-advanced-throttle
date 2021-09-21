<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use hamburgscleanest\GuzzleAdvancedThrottle\Helpers\UrlHelper;
use PHPUnit\Framework\TestCase;

class UrlHelperTest extends TestCase
{
    /** @test */
    public function removes_trailing_slash(): void
    {
        static::assertEquals('www.test.de', UrlHelper::removeTrailingSlash('www.test.de/'));
        static::assertEquals('www.test.de', UrlHelper::removeTrailingSlash('www.test.de'));
    }

    /** @test */
    public function prepends_slash_if_needed(): void
    {
        static::assertEquals('/api', UrlHelper::prependSlash('api'));
        static::assertEquals('/api', UrlHelper::prependSlash('/api'));
    }
}
