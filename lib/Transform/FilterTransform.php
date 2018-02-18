<?php

namespace PMeter\Transform;

use Generator;

class FilterTransform
{
    /**
     * @var array
     */
    private $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function __invoke(): Generator
    {
        $data = yield;

        while (true) {
            $data = yield array_filter($data, function ($key) {
                return in_array($key, $this->fields);
            }, ARRAY_FILTER_USE_KEY);
        }
    }
}
