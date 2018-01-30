<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Tests;

use DateInterval;
use DateTime;
use hamburgscleanest\GuzzleAdvancedThrottle\TimeKeeper;
use PHPUnit\Framework\TestCase;

/**
 * Class TimeKeeperTests
 * @package hamburgscleanest\GuzzleAdvancedThrottle\Tests
 */
class TimeKeeperTest extends TestCase
{

    /** @test
     * @throws \Exception
     */
    public function can_be_created_statically()
    {
        $timeKeeper = TimeKeeper::create(60);

        $this->assertInstanceOf(TimeKeeper::class, $timeKeeper);
    }

    /** @test
     * @throws \Exception
     */
    public function sets_correct_expiration_date()
    {
        $interval = 60;
        $timeKeeper = new TimeKeeper($interval);

        $minutesNow = + (new DateTime())->format('i');
        $timeKeeper->start();
        $minutesExpiration = + $timeKeeper->getExpiration()->format('i');

        $this->assertEquals($minutesNow + 1, $minutesExpiration);
    }

    /** @test
     * @throws \Exception
     */
    public function expiration_date_can_be_set_manually()
    {
        $interval = 60;
        $timeKeeper = new TimeKeeper($interval);

        $myExpiration = (new DateTime())->add(new DateInterval('PT120S'));
        $timeKeeper->setExpiration($myExpiration);

        $this->assertEquals($myExpiration, $timeKeeper->getExpiration());
    }

    /** @test
     * @throws \Exception
     */
    public function remaining_time_is_correct()
    {
        $interval = 60;
        $timeKeeper = new TimeKeeper($interval);
        $timeKeeper->start();
        $this->assertEquals($interval, $timeKeeper->getRemainingSeconds());
    }

    /** @test
     * @throws \Exception
     */
    public function is_expired_is_correct()
    {
        $interval = 60;
        $timeKeeper = new TimeKeeper($interval);
        $timeKeeper->start();
        $this->assertFalse($timeKeeper->isExpired());

        $timeKeeper->setExpiration((new DateTime())->sub(new DateInterval('P1D')));
        $this->assertTrue($timeKeeper->isExpired());
    }

    /** @test
     * @throws \Exception
     */
    public function resets_correctly()
    {
        $interval = 60;
        $timeKeeper = new TimeKeeper($interval);
        $timeKeeper->start();
        $timeKeeper->reset();

        $this->assertNull($timeKeeper->getExpiration());
    }
}