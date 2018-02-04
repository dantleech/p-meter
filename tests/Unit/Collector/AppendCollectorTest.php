<?php

namespace PMeter\Tests\Unit\Collector;

use PMeter\Tests\Unit\StepTestCase;
use PMeter\Collector\AppendCollector;

class AppendCollectorTest extends StepTestCase
{
    public function testAppendsIncomingData()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield 'hello';
                yield 'goodbye';
                yield 'bonjour';
                yield 'aurevoir';
            },
            new AppendCollector(),
        ])->run();

        $this->assertEquals([ 'hello', 'goodbye', 'bonjour', 'aurevoir' ], $result);
    }
}
