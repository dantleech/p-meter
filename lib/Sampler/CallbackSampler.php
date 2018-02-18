<?php

namespace PMeter\Sampler;

use Closure;
use Generator;

class CallbackSampler
{
    /**
     * @var Closure
     */
    private $closure;

    /**
     * @var int
     */
    private $revs;

    public function __construct(Closure $closure, int $revs)
    {
        $this->closure = $closure;
        $this->revs = $revs;
    }

    public function __invoke(): Generator
    {
        $data = yield;

        while (true) {
            $closure = $this->closure;
            $start = microtime(true);

            for ($i = 0; $i < $this->revs; $i++) {
                $closure($data);
            }

            $time = ((microtime(true) * 1E6) - ($start * 1E6)) / $this->revs;

            $data = yield array_merge($data, [
                'time' => $time
            ]);
        }
    }
}
