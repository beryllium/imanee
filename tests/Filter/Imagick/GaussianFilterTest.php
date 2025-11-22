<?php
/**
 * SepiaFilter Tests
 */

namespace imanee\tests\Filter\Imagick;


use Imanee\Filter\Imagick\GaussianFilter;
use PHPUnit\Framework\TestCase;

class GaussianFilterTest extends TestCase
{
    protected $model;

    public function setup(): void
    {
        $this->model = new GaussianFilter();
    }

    public function tearDown(): void
    {
        $this->model = null;
    }

    public function testShouldReturnName()
    {
        $this->assertEquals('filter_gaussian', $this->model->getName());
    }

    public function testShouldApplyFilter()
    {
        $imagick = $this->getMockBuilder('\Imagick')
            ->setMethods(['gaussianBlurImage'])
            ->getMock();

        $imagick->expects($this->once())
            ->method('gaussianBlurImage')
            ->with(2, 2);

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

        $this->model->apply($imanee, ['radius' => 2, 'sigma' => 2]);
    }
}
