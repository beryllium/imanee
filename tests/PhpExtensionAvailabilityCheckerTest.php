<?php

namespace Imanee\Tests;

use Imanee\PhpExtensionAvailabilityChecker;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_TestCase;

class PhpExtensionAvailabilityCheckerTest extends TestCase
{
    /**
     * @var PhpExtensionAvailabilityChecker;
     */
    private $PhpExtensionAvailabilityChecker;

    public function setUp(): void
    {
        $this->PhpExtensionAvailabilityChecker = new PhpExtensionAvailabilityChecker();
    }

    public function testShouldConfirmDefaultExtensionIsLoaded()
    {
        $this->assertTrue($this->PhpExtensionAvailabilityChecker->isLoaded('Core'));
    }

    public function testShouldConfirmUnknownExtensionIsNotLoaded()
    {
        $this->assertFalse($this->PhpExtensionAvailabilityChecker->isLoaded('foo-bar'));
    }
}
