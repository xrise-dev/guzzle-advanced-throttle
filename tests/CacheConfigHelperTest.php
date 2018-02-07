<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use hamburgscleanest\GuzzleAdvancedThrottle\Cache\Helpers\CacheConfigHelper;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\LaravelCacheDriverNotSetException;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

/**
 * Class CacheConfigHelperTest
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class CacheConfigHelperTest extends TestCase
{

    /** @test */
    public function throws_driver_not_set_exception()
    {
        $this->expectException(LaravelCacheDriverNotSetException::class);

        CacheConfigHelper::getDriver(new Repository());
    }

    /** @test
     */
    public function gets_driver()
    {
        $this->assertEquals('file', CacheConfigHelper::getDriver($this->_getCacheConfig()));
    }

    /**
     * @return Repository
     */
    private function _getCacheConfig() : Repository
    {
        return new Repository($this->_getConfig()->get('cache'));
    }

    /**
     * @return Repository
     */
    private function _getConfig() : Repository
    {
        return new Repository(require 'config/app.php');
    }

    /** @test
     */
    public function gets_container()
    {
        $container = CacheConfigHelper::getContainer($this->_getCacheConfig());

        $this->assertInstanceOf(Filesystem::class, $container->offsetGet('files'));
    }

    /** @test
     */
    public function gets_cache_manager()
    {
        $cacheManager = CacheConfigHelper::getCacheManager($this->_getConfig());

        $this->assertEquals('file', $cacheManager->getDefaultDriver());
    }
}
