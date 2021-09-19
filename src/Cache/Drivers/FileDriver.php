<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache\Drivers;

use Illuminate\Filesystem\Filesystem;


class FileDriver extends LaravelDriver
{
    protected function _setContainer(): void
    {
        $this->_container['files'] = new Filesystem();
    }
}
