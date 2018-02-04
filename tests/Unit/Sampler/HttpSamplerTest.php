<?php

namespace PMeter\Tests\Unit\Sampler;

use PMeter\Tests\Unit\StepTestCase;
use PMeter\Sampler\HttpSampler;
use PMeter\Flow\Take;

class HttpSamplerTest extends StepTestCase
{
    public function testSample()
    {
        $result = $this->pipeline([
            new Take(1),
            new HttpSampler('http://localhost')
        ])->run();

        $this->assertArrayHasKey('http_code', $result);
    }
}
