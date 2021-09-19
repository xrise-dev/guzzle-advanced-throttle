<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Interfaces;

use DateTimeImmutable;

interface Clock
{
    public function __construct(DateTimeImmutable|null $start = null);

    public function now(): DateTimeImmutable;
}
