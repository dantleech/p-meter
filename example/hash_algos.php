<?php

use PMeter\Pipeline;
use PMeter\Flow\Take;
use PMeter\Sampler\HttpSampler;
use PMeter\Collector\AppendCollector;
use PMeter\Splitter\RotarySplitter;
use PMeter\Splitter\ThreadingSplitter;
use PMeter\Output\StreamOutput;
use PMeter\Transform\CsvTransform;
use PMeter\Transform\ImplodeTransform;
use PMeter\Transform\FilterTransform;
use PMeter\Transform\TableTransform;
use PMeter\Transform\AnsiRedrawTransform;
use PMeter\Transform\SummaryTransform;
use PMeter\Transform\BarChartTransform;
use PMeter\Flow\Batch;
use PMeter\Sampler\CallbackSampler;

require __DIR__ . '/../vendor/autoload.php';

$result = (new Pipeline([
    function () {
        yield;
        while (true) {
            foreach (array_slice(hash_algos(), 0, 10) as $algo) {
                yield [
                    'algo' => $algo,
                ];
            }
        }
    },
    new CallbackSampler(function ($params) {
        hash($params['algo'], 'Hello World');
    }, 1000),
    new SummaryTransform([ 'algo' ], ['time']),
    new RotarySplitter([
        new BarChartTransform('algo', 'time-mean'),
        new TableTransform(),
        function () {
            $data = yield;

            while (true) {
                $usage = [];
                $usage[] = 'Memory: ' . number_format(memory_get_usage(), 2);
                $usage[] = '  Peak: ' . number_format(memory_get_peak_usage(), 2);
                $usage[] = '  Real: ' . number_format(memory_get_usage(true), 2);
                $data = yield implode(PHP_EOL, $usage);
            }
        },
    ]),
    new Batch(3),
    new ImplodeTransform(),
    new AnsiRedrawTransform(),
    new StreamOutput(fopen('php://stdout', 'w')),
]))->run();
