<?php

namespace PMeter\Collector;

use Generator;

class AppendCollector
{
    public function __invoke(): Generator
    {
        $collection = [];
        $data = yield;

        while (true) {
            $collection [] = $data;
            $data = yield $collection;
        }
    }
}
