<?php

namespace PMeter\Generator;

use Generator;

class ParameterGenerator
{
    /**
     * @var array
     */
    private $values;

    /**
     * @var string
     */
    private $name;

    public function __construct(string $name, array $values)
    {
        $this->values = array_values($values);
        $this->name = $name;
    }

    public function __invoke(): Generator
    {
        $data = yield;
        $size = count($this->values);
        while (true) {
            for ($i = 0; $i < $size; $i++) {
                $data = yield array_merge([
                    $this->name => $this->values[$i]
                ]);
            }
        }
    }
}
