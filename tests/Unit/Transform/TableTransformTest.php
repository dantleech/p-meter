<?php

namespace PMeter\Tests\Unit\Transform;

use PMeter\Tests\Unit\StepTestCase;
use PMeter\Transform\TableTransform;

class TableTransformTest extends StepTestCase
{
    public function testNullInput()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield null;
            },
            new TableTransform()
        ])->run();

        $this->assertEquals(<<<'EOT'
EOT
        , $result);
    }

    public function testScalarInput()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield 'hello';
            },
            new TableTransform()
        ])->run();

        $this->assertEquals(<<<'EOT'
0     
-     
hello 

EOT
        , $result);
    }

    public function test1DArray()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [ 'one' => 1, 'two' => 2, 'three' => 3 ];
            },
            new TableTransform()
        ])->run();

        $this->assertEquals(<<<'EOT'
0 
- 
1 
2 
3 

EOT
        , $result);
    }

    public function test2DArray()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [ 
                    [ 'one' => 1, 'two' => 2, 'three' => 3 ],
                    [ 'one' => 4, 'two' => 5, 'three' => 6 ],
                ];
            },
            new TableTransform()
        ])->run();

        $this->assertEquals(<<<'EOT'
one two three 
--- --- ----- 
1   2   3     
4   5   6     

EOT
        , $result);
    }

    public function test3DArray()
    {
        $result = $this->pipeline([
            function () {
                yield;
                yield [ 
                    [ 'one' => [ 1 ], 'two' => [ 'one' => 2 ]],
                    [ 'one' => [ 4 ], 'two' => [ 'two '=> 5 ]],
                ];
            },
            new TableTransform()
        ])->run();

        $this->assertEquals(<<<'EOT'
one two        
--- ---        
[1] {"one":2}  
[4] {"two ":5} 

EOT
        , $result);
    }
}
