<?php
/**
 * ModulateFilter Tests
 */

namespace imanee\tests\Filter\Imagick;


use Imanee\Filter\Imagick\ModulateFilter;
use PHPUnit\Framework\TestCase;

class ModulateFilterTest extends TestCase
{
    protected $model;

    public function setup(): void
    {
        $this->model = new ModulateFilter();
    }

    public function tearDown(): void
    {
        $this->model = null;
    }

    public function testShouldReturnName()
    {
        $this->assertEquals('filter_modulate', $this->model->getName());
    }

    public function testShouldApplyFilter()
    {
        $imagick = $this->getMockBuilder('\Imagick')
            ->setMethods(['modulateImage'])
            ->getMock();

        $imagick->expects($this->once())
            ->method('modulateImage')
            ->with(90, 50, 90);

        $imanee = $this->getMockBuilder('Imanee\Imanee')
            ->setMethods(['getResource'])
            ->getMock();

        $imresource = $this->getMockBuilder('Imanee\ImageResource\ImagickResource')
            ->setMethods(['getResource'])
            ->getMock();

        $imanee->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($imresource));

        $imresource->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($imagick));


        $this->model->apply($imanee, [
            'brightness' => 90,
            'saturation' => 50,
            'hue'        => 90,
        ]);
    }
}
