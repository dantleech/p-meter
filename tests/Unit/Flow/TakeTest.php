<?php

namespace PMeter\Tests\Unit\Flow;

use PMeter\Flow\Take;
use PMeter\Tests\Unit\StepTestCase;

class TakeTest extends StepTestCase
{
    public function testTakesZero()
    {
        $take = $this->pipeline([ 
            new Take(0),
            function () {
                yield;
                throw new \Exception('No thanks');
            },
        ]);

        $result = $take->run(0);

        $this->assertEquals(0, $result);
    }

    public function testTakesQuantity()
    {
        $take = $this->pipeline([ 
            new Take(2),
            function () {
                $count = yield;
                while(true) {
                    $count++;
                    yield $count;
                }
            },
        ]);

        $result = $take->run(0);
        $this->assertEquals(2, $result);
    }
}
