<?php

namespace PMeter\Tests\Unit\Transform;

use PMeter\Tests\Unit\StepTestCase;
use PMeter\Transform\FilterTransform;

class FilterTransformTest extends StepTestCase
{
    public function testFilter()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [ 'one' => 1, 'two' => 2, 'three' => 3];
            },
            new FilterTransform(['one', 'three'])
        ])->run();

        $this->assertEquals([ 'one' => 1, 'three' => 3], $result);
    }
}
