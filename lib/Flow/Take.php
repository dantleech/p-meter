<?php

namespace PMeter\Flow;

use Generator;

class Take
{
    /**
     * @var int
     */
    private $quantity;

    public function __construct(int $quantity)
    {
        $this->quantity = $quantity;
    }

    public function __invoke(): Generator
    {
        $data = yield;

        for ($i = 0; $i < $this->quantity; $i++) {
            $data = yield $data;
        }
    }
}
