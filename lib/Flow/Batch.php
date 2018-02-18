<?php

namespace PMeter\Flow;

use Generator;

class Batch
{
    /**
     * @var int
     */
    private $size;

    public function __construct(int $size)
    {
        $this->size = $size;
    }

    public function __invoke(): Generator
    {
        $data = yield;
        $batch = [];
        $index = 0;

        while (true) {
            $index++;
            $batch[] = $data;

            // TODO: We should stop the flow here somehow and only pass
            //       _batches_ downstream.
            $data = yield $batch;

            if (0 === $index % $this->size) {
                $batch = [];
            }
        }
    }
}
