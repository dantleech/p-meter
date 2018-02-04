<?php

namespace PMeter\Splitter;

use Generator;

class RotarySplitter
{
    /**
     * @var array
     */
    private $steps;

    public function __construct(array $steps)
    {
        $this->steps = $steps;
    }

    public function __invoke(): Generator
    {
        $data = yield;
        $index = 0;
        $stepCount = count($this->steps);

        while (true) {
            $offset = $index++ % $stepCount;
            $generator = $this->steps[$offset]();
            $data = $generator->send($data);

            $data = yield $data;
        }
    }
}
