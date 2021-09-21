<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateTimeImmutable;
use hamburgscleanest\GuzzleAdvancedThrottle\SystemClock;
use PHPUnit\Framework\TestCase;

class SystemClockTest extends TestCase
{
    /** @test */
    public function now_returns_correct_value(): void
    {
        $now = new DateTimeImmutable();

        static::assertEquals($now, SystemClock::create($now)->now());
    }

    /** @test */
    public function sets_correct_timestamp(): void
    {
        $timestamp = 1337;

        static::assertEquals($timestamp, SystemClock::fromTimestamp($timestamp)->now()->getTimestamp());
    }

    /** @test */
    public function advances_correct_amount_of_seconds(): void
    {
        static::assertEquals(
            '15',
            SystemClock::create(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2021-09-20 12:00:00'))
                ->advanceSeconds(15)
                ->now()
                ->format('s')
        );
    }

    /** @test */
    public function advances_correct_amount_of_minutes(): void
    {
        static::assertEquals(
            '15',
            SystemClock::create(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2021-09-20 12:00:00'))
                ->advanceMinutes(15)
                ->now()
                ->format('i')
        );
    }
}
