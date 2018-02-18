<?php

namespace PMeter\Tests\Unit\Generator;

use PMeter\Tests\Unit\StepTestCase;
use PMeter\Flow\Take;
use PMeter\Generator\ParameterGenerator;
use PMeter\Collector\AppendCollector;

class ParameterGeneratorTest extends StepTestCase
{
    public function testYieldsParameters()
    {
        $results = $this->pipeline([
            new ParameterGenerator('param', [ 'one', 'two', 'three' ]),
            new Take(6),
            new AppendCollector(),
        ])->run();

        $this->assertEquals([
            ['param' => 'one'],
            ['param' => 'two'],
            ['param' => 'three'],
            ['param' => 'one'],
            ['param' => 'two'],
            ['param' => 'three'],
        ], $results);
    }
}
