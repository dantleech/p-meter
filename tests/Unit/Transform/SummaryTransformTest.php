<?php

namespace PMeter\Tests\Unit\Transform;

use PMeter\Tests\Unit\StepTestCase;
use PMeter\Transform\SummaryTransform;
use InvalidArgumentException;

class SummaryTransformTest extends StepTestCase
{
    public function testNullInput()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('passed an array input');
        $result = $this->pipeline([
            function () {
                yield;
                yield;
            },
            new SummaryTransform([], [])
        ])->run();
    }

    public function testScalarInput()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('passed an array input');
        $result = $this->pipeline([
            function () {
                yield;
                yield 'hello';
            },
            new SummaryTransform([], [])
        ])->run();
    }

    public function testExceptionIfGroupFieldNotExisting()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Group by field "abc" does not exist in input with fields "url", "time"');

        $result = $this->pipeline([
            function () {
                yield;
                yield [
                    'url' => 'www.example.com',
                    'time' => 10,
                ];
            },
            new SummaryTransform(['abc'], [])
        ])->run();
    }

    public function testSummarizeNoFields()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [
                    'url' => 'www.example.com',
                    'time' => 10,
                ];
            },
            new SummaryTransform(['url'])
        ])->run();

        $this->assertEquals([
            'www.example.com' => [
                'hash' => 'www.example.com',
            ],
        ], $result);
    }

    public function testSummarizeField()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [
                    'url' => 'www.example.com',
                    'time' => 20,
                ];
                yield [
                    'url' => 'www.example.com',
                    'time' => 10,
                ];
            },
            new SummaryTransform(['url'], ['time'])
        ])->run();

        $this->assertEquals([
            'www.example.com' => [
                'samples' => 2,
                'hash' => 'www.example.com',
                'time-mean' => 15,
                'time-min' => 10,
                'time-max' => 20,
            ],
        ], $result);
    }
}
