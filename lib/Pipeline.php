<?php

namespace PMeter;

use PMeter\Stage;
use Generator;

// 
class Pipeline implements Stage
{
    /**
     * @var Generator[]
     */
    private $generators;

    public function __construct(array $stages = [])
    {
        foreach ($stages as $stage) {
            $this->generators[] = $stage([]);
        }
    }

    public function __invoke(): Generator
    {
        // get the input
        $data = yield;

        // repeat until one of the generators is not valid
        while (true) {
            // run all of the stage generators sequentially, passing the result
            // of the previous stage to the next stage.
            for ($index = 0; $index < count($this->generators); $index++) {
                $generator = $this->generators[$index];
                $data = $generator->send($data);

                if (false === $generator->valid()) {
                    return $data;
                }
            }

            // yield the last result
            yield $data;
        }
    }

    public function run(array $data = []): array
    {
        $generator = $this->__invoke();

        // send the initial value and trigger the first iteration
        $generator->send($data);

        // iterate the rest of the pipeline
        while ($generator->valid()) {
            $data = $generator->current();
            $generator->next();
        }

        return $data;
    }
}
