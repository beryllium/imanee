<?php

namespace Imanee\Tests;

use Imanee\Exception\ExtensionNotFoundException;
use Imanee\Exception\UnsupportedFormatException;
use Imanee\ResourceProvider;
use PHPUnit\Framework\TestCase;
use Mockery;

class ResourceProviderTest extends TestCase
{
    /**
     * @var Mockery\MockInterface
     */
    private $PhpExtensionAvailabilityChecker;

    /**
     * @var ResourceProvider
     */
    private $resourceProvider;

    public function setUp(): void
    {
        $this->PhpExtensionAvailabilityChecker = Mockery::mock('Imanee\PhpExtensionAvailabilityChecker');
        $this->resourceProvider = new ResourceProvider($this->PhpExtensionAvailabilityChecker);
    }

    public function testshouldFailIfNoUsableExtensionIsAvailable()
    {
        $this->expectException(ExtensionNotFoundException::class);

        $this->PhpExtensionAvailabilityChecker
            ->shouldReceive('isLoaded')
            ->with('imagick')
            ->andReturn(false);

        $this->PhpExtensionAvailabilityChecker
            ->shouldReceive('isLoaded')
            ->with('gd')
            ->andReturn(false);

        $this->resourceProvider->createImageResource();
    }

    public function testShouldFailReturnGdResourceIfImagickIsNotAvailable()
    {
        $this->PhpExtensionAvailabilityChecker
            ->shouldReceive('isLoaded')
            ->with('imagick')
            ->andReturn(false);
        $this->PhpExtensionAvailabilityChecker
            ->shouldReceive('isLoaded')
            ->with('gd')
            ->andReturn(true);

        $this->assertInstanceOf('Imanee\ImageResource\GDResource', $this->resourceProvider->createImageResource());

    }

    public function testShouldReturnImagickResource()
    {
        $this->PhpExtensionAvailabilityChecker
            ->shouldReceive('isLoaded')
            ->with('imagick')
            ->andReturn(true);

        $this->assertInstanceOf('Imanee\ImageResource\ImagickResource', $this->resourceProvider->createImageResource());
    }
}
