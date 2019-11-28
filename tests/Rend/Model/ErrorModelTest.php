<?php

namespace Rend\Model;

use PHPUnit\Framework\TestCase;
use Rend\View\Model\ErrorModel;

class ErrorModelTest extends TestCase
{
    public function testTrue()
    {
        $model = new ErrorModel(
            new \Exception(
                'parent',
                0,
                new \Exception('child')
            )
        );

        $expected = [
            [
                'code' => 0,
                'file' => __FILE__,
                'line' => 13,
                'message' => 'parent'
            ],
            [
                'code' => 0,
                'file' => __FILE__,
                'line' => 16,
                'message' => 'child'
            ],
        ];

        $actual = $model->getVariables();

        $this->assertEquals($expected, $actual);
    }
}