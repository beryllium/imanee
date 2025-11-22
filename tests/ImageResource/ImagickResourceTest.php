<?php

namespace Imanee\Tests;

use Imanee\Exception\EmptyImageException;
use Imanee\Exception\UnsupportedFormatException;
use Imanee\Imanee;
use Imanee\ImageResource\ImagickResource;
use PHPUnit\Framework\TestCase;

class ImagickResourceTest extends TestCase
{
    protected $test_jpg;
    protected $animated_gif;
    protected $model;

    public function setup(): void
    {
        $this->test_jpg     = __DIR__ . '/../resources/img01.jpg';
        $this->animated_gif = __DIR__ . '/../resources/animated.gif';
        $this->model        = new ImagickResource();
    }

    public function tearDown(): void
    {
        $this->test_jpg     = null;
        $this->animated_gif = null;
        $this->model        = null;
    }

    public function testShouldClone()
    {
        $clone = clone $this->model;

        $this->assertEquals($clone, $this->model);
    }

    public function testShouldCreateNew()
    {
        $resource = $this->getMockBuilder('\Imagick')
            ->disableOriginalConstructor()
            ->setMethods(['newImage', 'getImageGeometry'])
            ->getMock();

        $resource->expects($this->once())
            ->method('newImage')
            ->with(100, 100);

        $resource->expects($this->once())
            ->method('getImageGeometry');

        $this->model->setResource($resource);
        $this->model->createNew(100, 100);
    }

    public function testShouldSetFormat()
    {
        $this->model->createNew(200, 200);
        $this->model->setFormat('jpeg');

        $this->assertEquals('JPEG', $this->model->getFormat());
    }

    public function testShouldResizeImageProportional()
    {
        $this->model->createNew(100, 100);
        $this->model->setFormat('jpeg');

        $this->model->resize(50, 80);

        $this->assertEquals(50, $this->model->width);
        $this->assertEquals(50, $this->model->height);
    }

    public function testShouldResizeImageExact()
    {
        $this->model->createNew(100, 100);
        $this->model->setFormat('jpeg');

        $this->model->resize(50, 80, false);

        $this->assertEquals(50, $this->model->width);
        $this->assertEquals(80, $this->model->height);
    }

    public function testShouldNotResizeIfBlank()
    {
        $this->expectException(EmptyImageException::class);

        $this->model->resize(50, 80);
    }

    public function testShouldSetAndGetBackground()
    {
        $this->model->createNew(100, 100, 'black');

        $this->assertEquals('black', $this->model->getBackground());
    }

    public function testBlankImage()
    {
        $this->assertTrue($this->model->isBlank());

        $this->model->createNew(100, 100);

        $this->assertFalse($this->model->isBlank());
    }

    public function testGetGifFrameShouldThrowExceptionIfWrongFormat()
    {
        $this->expectException(UnsupportedFormatException::class);

        $this->model->load($this->test_jpg);
        $this->model->getGifFrames();
    }

    public function testGetGifFrames()
    {
        $this->model->load($this->animated_gif);
        $imanee = $this->model->getGifFrames();

        $this->assertCount(4, $imanee->getFrames());
    }
}
