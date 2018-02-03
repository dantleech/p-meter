<?php

namespace PMeter;

use Generator;
use InvalidArgumentException;

class Pipeline
{
    /**
     * @var Generator[]
     */
    private $generators;

    /**
     * The pipeline accepts an array of stages.
     * Stages MUST be callable and each stage MUST return a generator.
     */
    public function __construct(array $stages = [])
    {
        foreach ($stages as $stage) {
            if (false === is_callable($stage)) {
                throw new InvalidArgumentException(sprintf(
                    'Stage must be a callable (e.g. closure, invokable class, or other callback)'
                ));
            }

            $generator = $stage([]);

            if (false === $generator instanceof Generator) {
                throw new InvalidArgumentException(sprintf(
                    'Callable must return a Generator instance, got "%s"',
                    is_object($generator) ? get_class($generator) : gettype($generator)
                ));
            }

            $this->generators[] = $generator;
        }
    }

    public function __invoke(): Generator
    {
        // get the input
        $data = yield;

        // repeat until one of the generators is exhausted (not valid)
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

    /**
     * Run the pipeline.
     *
     * Run all of the stages in the pipeline sequentially.
     * An initial value can be passed.
     */
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
