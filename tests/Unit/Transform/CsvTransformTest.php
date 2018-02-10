<?php

namespace PMeter\Tests\Unit\Transform;

use PHPUnit\Framework\TestCase;
use PMeter\Tests\Unit\StepTestCase;
use PMeter\Transform\CsvTransform;
use PMeter\Collector\AppendCollector;

class CsvTransformTest extends StepTestCase
{
    public function testCsvTransform()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [ 'one', 'two', 'three' ];
                yield [ 'one', 'two', 'three' ];
            },
            new CsvTransform()
        ])->run();

        $this->assertEquals(<<<'EOT'
one,two,three

EOT
        , $result);
    }

    public function testComplexValues()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [ 'one', [ 'one' => 'two '], 'three' ];
            },
            new CsvTransform()
        ])->run();

        $this->assertEquals('one,<array>,three' . PHP_EOL, $result);
    }

    public function testHeader()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [ 'hello' => 'one', 'goodbye' => 'three' ];
                yield [ 'hello' => 'one', 'goodbye' => 'three' ];
            },
            new CsvTransform(',', true),
            new AppendCollector(),
        ])->run();
        $result = implode('', $result);

        $this->assertEquals(<<<'EOT'
hello,goodbye
one,three
one,three

EOT
        , $result);
    }

    public function testMissingValues()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [ 'hello' => '', 'goodbye' => 'three' ];
                yield [ 'hello' => '', 'goodbye' => 'three' ];
            },
            new CsvTransform(',', true),
            new AppendCollector(),
        ])->run();
        $result = implode('', $result);

        $this->assertEquals(<<<'EOT'
hello,goodbye
,three
,three

EOT
        , $result);
    }
}
