<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use hamburgscleanest\GuzzleAdvancedThrottle\WildcardMatcher;
use PHPUnit\Framework\TestCase;

/**
 * Class WildcardMatcherTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class WildcardMatcherTest extends TestCase
{

    /** @test
     */
    public function matches_wildcards() : void
    {
        $this->assertTrue(WildcardMatcher::matches('test1.{wildcard1}.test2.{wildcard2}.test3', 'test1.replace1.test2.replace2.test3'));
    }

    /** @test
     */
    public function does_not_match_when_not_all_wildcards_given() : void
    {
        $wildcardText = 'test1.{wildcard1}.test2.{wildcard2}.test3';

        $this->assertFalse(WildcardMatcher::matches($wildcardText, 'no_wildcards'));
        $this->assertFalse(WildcardMatcher::matches($wildcardText, 'test1.{wildcard1}.test2.test3'));
        $this->assertFalse(WildcardMatcher::matches($wildcardText, 'test1.{wildcard1}.test2.{wildcard2}'));
        $this->assertFalse(WildcardMatcher::matches($wildcardText, 'one.{wildcard1}.two.{wildcard2}.three'));
    }

    /** @test
     */
    public function ignores_text_without_wildcard() : void
    {
        $wildcardText = 'test1';

        $this->assertTrue(WildcardMatcher::matches($wildcardText, $wildcardText));
        $this->assertFalse(WildcardMatcher::matches($wildcardText, 'test2'));
    }
}