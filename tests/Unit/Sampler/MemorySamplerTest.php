<?php

namespace PMeter\Tests\Unit\Sampler;

use PHPUnit\Framework\TestCase;
use PMeter\Sampler\MemorySampler;
use PMeter\Tests\Unit\StepTestCase;
use PMeter\Flow\Take;

class MemorySamplerTest extends StepTestCase
{
    public function testSample()
    {
        $result = $this->pipeline([
            new MemorySampler(),
            new Take(1),
        ])->run();

        $this->assertArrayHasKey('mem', $result);
        $this->assertArrayHasKey('mem-real', $result);
        $this->assertArrayHasKey('mem-peak', $result);
    }
}
