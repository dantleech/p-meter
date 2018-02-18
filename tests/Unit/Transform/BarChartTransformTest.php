<?php

namespace PMeter\Tests\Unit\Transform;

use PMeter\Tests\Unit\StepTestCase;
use PMeter\Transform\BarChartTransform;

class BarChartTransformTest extends StepTestCase
{
    /**
     * @dataProvider provideBar
     */
    public function testBar(int $width, array $data, string $expected)
    {
        $result = $this->pipeline([
            function () use ($data) {
                yield;
                yield $data;
            },
            new BarChartTransform('label', 'time', $width),
        ])->run();

        $this->assertEquals($expected, $result);
    }

    public function provideBar()
    {
        return [
            [
                10,
                [
                    [
                        'label' => 'One',
                        'time' => 10,
                    ],
                    [
                        'label' => 'Two',
                        'time' => 5,
                    ],
                ],
                <<<'EOT'
One  |██████████ 10
Two  |█████      5

EOT
            ],
            [
                5,
                [
                    [
                        'label' => 1,
                        'time' => 10,
                    ],
                    [
                        'label' => 2,
                        'time' => 9,
                    ],
                    [
                        'label' => 3,
                        'time' => 9.5,
                    ],
                ],
                <<<'EOT'
1  |█████ 10
2  |████▌ 9
3  |████▊ 9.5

EOT
            ],
            [
                40,
                [
                    [
                        'label' => '1',
                        'time' => 1,
                    ],
                    [
                        'label' => '2',
                        'time' => 1.25,
                    ],
                    [
                        'label' => '3',
                        'time' => 1.5,
                    ],
                    [
                        'label' => '4',
                        'time' => 1.75,
                    ],
                    [
                        'label' => 5,
                        'time' => 2,
                    ],
                ],
                <<<'EOT'
1  |████████████████████                     1
2  |█████████████████████████                1.25
3  |██████████████████████████████           1.5
4  |███████████████████████████████████      1.75
5  |████████████████████████████████████████ 2

EOT
            ],
            [
                5,
                [
                    [ 'label' => '0.9', 'time' => 0.9 ],
                    [ 'label' => '0.91', 'time' => 0.91 ],
                    [ 'label' => '0.92', 'time' => 0.92 ],
                    [ 'label' => '0.93', 'time' => 0.93 ],
                    [ 'label' => '0.94', 'time' => 0.94 ],
                    [ 'label' => '0.95', 'time' => 0.95 ],
                    [ 'label' => '0.96', 'time' => 0.96 ],
                    [ 'label' => '0.97', 'time' => 0.97 ],
                    [ 'label' => '0.98', 'time' => 0.98 ],
                    [ 'label' => '0.99', 'time' => 0.99 ],
                ],
                <<<'EOT'
0.9   |████▌ 0.9
0.91  |████▌ 0.91
0.92  |████▋ 0.92
0.93  |████▋ 0.93
0.94  |████▋ 0.94
0.95  |████▊ 0.95
0.96  |████▊ 0.96
0.97  |████▉ 0.97
0.98  |████▉ 0.98
0.99  |█████ 0.99

EOT
            ],

        ];
    }
}
