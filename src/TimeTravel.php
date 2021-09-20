<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle;

use DateInterval;

trait TimeTravel
{
    private function _tick(int $value, string $unit = 'S'): self
    {
        $this->_now = $this->_now->add(new DateInterval('PT' . $value . $unit));

        return $this;
    }

    public function advanceSeconds(int $seconds): self
    {
        return $this->_tick($seconds, 'S');
    }

    public function advanceMinutes(int $minutes): self
    {
        return $this->_tick($minutes, 'M');
    }
}
