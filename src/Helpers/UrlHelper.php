<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Helpers;

class UrlHelper
{
    public static function removeTrailingSlash(string $path): string
    {
        return \rtrim($path, '/');
    }

    public static function prependSlash(string $path): string
    {
        return '/' . \ltrim($path, '/');
    }
}
