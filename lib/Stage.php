<?php

namespace PMeter;

use Generator;

interface Stage
{
    public function __invoke(): Generator;
}
