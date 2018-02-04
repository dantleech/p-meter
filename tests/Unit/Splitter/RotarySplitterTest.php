<?php

namespace PMeter\Tests\Unit\Splitter;

use PMeter\Tests\Unit\StepTestCase;
use PMeter\Flow\Take;
use PMeter\Splitter\RotarySplitter;

class RotarySplitterTest extends StepTestCase
{
    public function testPassesInputToEachStepSequentally()
    {
        $result = $this->pipeline([
            new RotarySplitter([
                function () {
                    $data = yield;
                    while (true) {
                        $data[] = 'one';
                        yield $data;
                    }
                },
                function () {
                    $data = yield;
                    while (true) {
                        $data[] = 'two';
                        yield $data;
                    }
                },
            ]),
            new Take(4),
        ])->run();

        $this->assertEquals([ 'one', 'two', 'one', 'two' ], $result);
    }
}
