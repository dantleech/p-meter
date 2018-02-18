<?php

namespace PMeter\Transform;

use Generator;

class ImplodeTransform
{
    /**
     * @var string
     */
    private $delimiter;

    public function __construct(string $delimiter = PHP_EOL)
    {
        $this->delimiter = $delimiter;
    }

    public function __invoke(): Generator
    {
        $data = yield;

        while (true) {
            $data = implode($this->delimiter, $data);
            $data = yield $data;
        }
    }
}
