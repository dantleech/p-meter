<?php

namespace PMeter\Tests\Unit\Output;

use PHPUnit\Framework\TestCase;
use PMeter\Tests\Unit\StepTestCase;
use PMeter\Output\StreamOutput;
use stdClass;

class StreamOutputTest extends StepTestCase
{
    public function testSimpleData()
    {
        $handle = fopen('php://memory', 'rw+');
        $result = $this->pipeline([
            function () {
                yield;
                yield 'hello';
            },
            new StreamOutput($handle)
        ])->run();

        rewind($handle);
        $result = stream_get_contents($handle);
        fclose($handle);

        $this->assertEquals('hello', $result);
    }

    public function testComplexDataArray()
    {
        $handle = fopen('php://memory', 'rw+');
        $result = $this->pipeline([
            function () {
                yield;
                yield [ 'hello' ];
            },
            new StreamOutput($handle)
        ])->run();

        rewind($handle);
        $result = stream_get_contents($handle);
        fclose($handle);

        $this->assertEquals('["hello"]', $result);
    }

    public function testComplexDataObjet()
    {
        $handle = fopen('php://memory', 'rw+');
        $result = $this->pipeline([
            function () {
                yield;
                yield new stdClass();
            },
            new StreamOutput($handle)
        ])->run();

        rewind($handle);
        $result = stream_get_contents($handle);
        fclose($handle);

        $this->assertEquals('{}', $result);
    }
}
