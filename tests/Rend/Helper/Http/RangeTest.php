<?php
namespace Rend\Helper\Http;

use PHPUnit\Framework\TestCase;
use Zend\Http\Headers;
use Zend\Http\PhpEnvironment\Request;

class RangeTest extends TestCase
{
    public function testFullValidRange()
    {
        $wrapper = new class {
            use Range;
            public function getRangeValue()
            {
                $request = new Request();
                $request->setHeaders(Headers::fromString('Range: 0-10'));
                return $this->getRange($request, 100);
            }
        };

        $expected = (new RangeValue())->setFrom(0)->setTo(10);
        $actual = $wrapper->getRangeValue();

        $this->assertEquals($expected, $actual);
    }
    public function testNoEndingRange()
    {
        $wrapper = new class {
            use Range;
            public function getRangeValue()
            {
                $request = new Request();
                $request->setHeaders(Headers::fromString('Range: 0-'));
                return $this->getRange($request, 100);
            }
        };

        $expected = (new RangeValue())->setFrom(0)->setTo(null);
        $actual = $wrapper->getRangeValue();

        $this->assertEquals($expected, $actual);
    }
}
