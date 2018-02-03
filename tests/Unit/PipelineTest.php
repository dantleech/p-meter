<?php

namespace PMeter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PMeter\Stage;
use PMeter\Pipeline;
use Generator;

class PipelineTest extends TestCase
{
    public function testSingleValueStep()
    {
        $pipeline = new Pipeline([
            new class implements Stage {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 'hello';
                    yield $data;
                }
            }
        ]);

        $result = $pipeline->run();
        $this->assertEquals(['hello'], $result);
    }

    public function testMultiValueStep()
    {
        $pipeline = new Pipeline([
            new class implements Stage {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 'hello';
                    $data = yield $data;
                    $data[] = 'goodbye';
                    yield $data;
                }
            }
        ]);

        $result = $pipeline->run();
        $this->assertEquals(['hello', 'goodbye'], $result);
    }

    public function testMultiStage()
    {
        $pipeline = new Pipeline([
            new class implements Stage {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 'hello';
                    yield $data;
                }
            },
            new class implements Stage {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 'goodbye';
                    yield $data;
                }
            }
        ]);

        $result = $pipeline->run();
        $this->assertEquals(['hello', 'goodbye'], $result);
    }

    public function testMultiStageTake()
    {
        $pipeline = new Pipeline([
            new class implements Stage {
                function __invoke(): Generator 
                { 
                    $data = [];
                    while (true) {
                        $data = yield $data;
                        $data[] = 'hello';
                    }
                }
            },
            new class implements Stage {
                function __invoke(): Generator 
                {
                    $data = [];
                    for ($i = 0; $i <= 3; $i++) {
                        $data = yield $data;
                    }
                }
            }
        ]);

        $result = $pipeline->run();
        $this->assertEquals(['hello', 'hello', 'hello'], $result);
    }

    public function testNestedPipeline()
    {
        $pipeline = new Pipeline([
            new class implements Stage {
                function __invoke(): Generator 
                { 
                    yield;
                    yield [ 'one' ];
                }
            },
            new Pipeline([
                new class implements Stage {
                    function __invoke(): Generator 
                    { 
                        $data = yield;
                        $data[] = 'two';
                        yield $data;
                    }
                },
                new class implements Stage {
                    function __invoke(): Generator 
                    { 
                        $data = yield;
                        $data[] = 'three';
                        yield $data;
                    }
                },
            ]),
            new class implements Stage {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 'four';
                    yield $data;
                }
            },
        ]);

        $result = $pipeline->run();
        $this->assertEquals(['one', 'two', 'three', 'four'], $result);
    }

    public function testPrimesInput()
    {
        $pipeline = new Pipeline([
            new class implements Stage {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 1;
                    yield $data;
                }
            },
        ]);
        $result = $pipeline->run([ 0 ]);
        $this->assertEquals([0, 1], $result);
    }
}
