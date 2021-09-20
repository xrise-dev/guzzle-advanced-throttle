<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Interfaces;

use DateTimeImmutable;

interface Clock
{
    public function __construct(DateTimeImmutable|null $now = null);

    public static function create(DateTimeImmutable|null $now = null): self;

    public static function fromTimestamp(int $timestamp): self;

    public function now(): DateTimeImmutable;
}
