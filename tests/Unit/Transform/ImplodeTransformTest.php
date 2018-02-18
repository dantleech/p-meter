<?php

namespace PMeter\Tests\Unit\Transform;

use PMeter\Tests\Unit\StepTestCase;
use PMeter\Transform\ImplodeTransform;

class ImplodeTransformTest extends StepTestCase
{
    public function testImplode()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [ 'one', 'two', 'three' ];
            },
            new ImplodeTransform()
        ])->run();

        $this->assertEquals(<<<'EOT'
one
two
three
EOT
        , $result);
    }

    public function testWithDelimiter()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [ 'one', 'two', 'three' ];
            },
            new ImplodeTransform(',')
        ])->run();

        $this->assertEquals(<<<'EOT'
one,two,three
EOT
        , $result);
    }
}
