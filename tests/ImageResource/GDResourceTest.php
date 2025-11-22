<?php
namespace Imanee\Tests;

use Imanee\Exception\ImageNotFoundException;
use Imanee\Exception\InvalidColorException;
use Imanee\Exception\UnsupportedFormatException;
use Imanee\Exception\UnsupportedMethodException;
use Imanee\ImageResource\GDResource;
use PHPUnit\Framework\TestCase;

class GDResourceTest extends TestCase
{
    /**
     * @covers \Imanee\ImageResource\GDResource::load
     * @group imanee-33
     */
    public function testExceptionIsThrownIfImagePathIsNotFound()
    {
        $this->expectException(ImageNotFoundException::class);
        $this->expectExceptionMessage("File '/path/to/nowhere' not found. Are you sure this is the right path?");

        $imageResource = new GDResource();
        $imageResource->load('/path/to/nowhere');
    }

    public function imageTypeProvider()
    {
        return [
            ['jpg', 'image/jpeg'],
            ['gif', 'image/gif'],
            ['png', 'image/png'],
        ];
    }

    /**
     * @covers \Imanee\ImageResource\GDResource::load
     * @group imanee-33
     * @todo \Imanee\ImageResource\GDResource::load uses static method Imanee::getImageInfo
     */
    public function testLoadingUnsupportedImageThrowsException()
    {
        $this->expectException(UnsupportedFormatException::class);
        $this->expectExceptionMessage("The format 'image/tiff' is not supported by this Resource.");

        $file = __DIR__ . '/_files/imanee.tiff';
        $imageResource = new GDResource();
        $imageResource->load($file);
    }
    /**
     * @covers \Imanee\ImageResource\GDResource::loadColor
     * @group imanee-33
     * @dataProvider imageTypeProvider
     * @todo \Imanee\ImageResource\GDResource::load uses static method Imanee::getImageInfo
     */
    public function testLoadingImageCreatesAnImage($ext, $mime)
    {
        $file = __DIR__ . '/_files/imanee.' . $ext;
        $imageResource = new GDResource();
        $result = $imageResource->load($file);

        $this->assertSame($mime, $imageResource->mime);
        $this->assertSame($ext, $imageResource->format);

        $this->assertInstanceOf('\\Imanee\ImageResource\\GDResource', $result);
        $this->assertSame($imageResource, $result);
    }

    public function badColorProvider()
    {
        return [
            ['AABBCCDD'],
            ['foobar'],
            ['#1234567'],
        ];
    }

    /**
     * @covers \Imanee\ImageResource\GDResource::load
     * @group imanee-33
     * @dataProvider badColorProvider
     */
    public function testLoadColourFailsWithBadInput($color)
    {
        $this->expectException(InvalidColorException::class);

        $file = __DIR__ . '/_files/imanee.png';
        $gdResource = new GDResource();
        $gdResource->load($file);
        $this->assertFalse($gdResource->loadColor($color));
    }

    /**
     * @covers \Imanee\ImageResource\GDResource::output
     * @dataProvider imageProvider
     */
    public function testReturnTheImageInsteadOfPuttingItInTheBuffer($imageRelativePath)
    {
        $file = __DIR__ . $imageRelativePath;
        $gdResource = new GDResource();
        $gdResource->load($file);

        ob_start();
        $image = $gdResource->output();
        $buffer = ob_get_contents();
        ob_end_clean();

        $this->assertNotEmpty($image);
        $this->assertEmpty($buffer);
    }

    /**
     * @covers \Imanee\ImageResource\GDResource::output
     */
    public function testThrowErrorWhenFormatNotSupported()
    {
        $this->expectException(UnsupportedFormatException::class);

        $file = __DIR__ . '/_files/imanee.png';;
        $gdResource = new GDResource();
        $gdResource->load($file);

        $gdResource->output('wrongFormat');
    }

    public function imageProvider()
    {
        return [
            ['/_files/imanee.png'],
            ['/_files/imanee.jpg'],
            ['/_files/imanee.gif'],
        ];
    }

    public function testGetGifFramesShouldThrowExceptionAsUnsupported()
    {
        $this->expectException(UnsupportedMethodException::class);

        $imageResource = new GDResource();
        $imageResource->getGifFrames();
    }
}
