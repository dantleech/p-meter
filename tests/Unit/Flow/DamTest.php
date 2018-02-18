<?php

namespace PMeter\Tests\Unit\Flow;

use PMeter\Tests\Unit\StepTestCase;
use PMeter\Flow\Dam;

class DamTest extends StepTestCase
{
    public function testNullifiesInput()
    {
        $take = $this->pipeline([ 
            function () {
                yield;
                yield 'one';
            },
            new Dam(),
        ]);

        $result = $take->run();

        $this->assertNull($result);
    }
}
