<?php

namespace PMeter\Flow;

use Generator;

class Dam
{
    public function __invoke(): Generator
    {
        yield;

        while (true) {
            yield;
        }
    }
}
