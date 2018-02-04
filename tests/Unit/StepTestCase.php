<?php

namespace PMeter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PMeter\Pipeline;

class StepTestCase extends TestCase
{
    public function pipeline(array $steps)
    {
        return new Pipeline($steps);
    }
}
