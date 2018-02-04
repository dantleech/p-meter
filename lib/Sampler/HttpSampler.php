<?php

namespace PMeter\Sampler;

use Generator;

class HttpSampler
{
    /**
     * @var string
     */
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function __invoke(): Generator
    {
        $data = yield;

        while (true) {
            $info = $this->sampleUrl();

            yield array_merge((array) $data, [
                'url' => $this->url,
            ], $info);
        }
    }

    private function sampleUrl()
    {
        $handle = curl_init($this->url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_exec($handle);
        $info = curl_getinfo($handle);
        curl_close($handle);

        return $info;
    }
}
