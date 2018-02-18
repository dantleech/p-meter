<?php

namespace PMeter\Output;

use Generator;

class StreamOutput
{
    /**
     * @var string
     */
    private $stream;

    public function __construct($stream)
    {
        $this->stream = $stream;
    }

    public function __invoke(): Generator
    {
        $data = yield;

        while (true) {
            if (!is_scalar($data)) {
                $data = json_encode($data);
            }
            fwrite($this->stream, $data);
            $data = yield;
        }
    }
}
