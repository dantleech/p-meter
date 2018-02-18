<?php

namespace PMeter\Sampler;

use Generator;

class MemorySampler
{
    public function __invoke(): Generator
    {
        $data = yield;

        while (true) {
            $data = (array) $data;
            $data = yield array_merge($data, [
                'mem' => memory_get_usage(),
                'mem-real' => memory_get_usage(true),
                'mem-peak' => memory_get_peak_usage(),
            ]);
        }
    }
}
