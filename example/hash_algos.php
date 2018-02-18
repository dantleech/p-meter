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
use PMeter\Generator\ParameterGenerator;
use PMeter\Sampler\MemorySampler;
use PMeter\Flow\Dam;

require __DIR__ . '/../vendor/autoload.php';

$result = (new Pipeline([
    new ParameterGenerator('algo', array_slice(hash_algos(), 0, 10)),
    new CallbackSampler(function ($params) {
        hash($params['algo'], 'Hello World');
    }, 100),
    new SummaryTransform([ 'algo' ], ['time']),
    new RotarySplitter([
        new BarChartTransform('hash', 'time-mean'),
        new TableTransform(),
        new Pipeline([
            new Dam(),
            new MemorySampler(),
            function () {
                $data = yield;
                while(true) {
                    $data = yield [ $data ];
                }
            },
            new TableTransform(),
        ]),
    ]),
    new Batch(3),
    new ImplodeTransform(),
    new AnsiRedrawTransform(),
    new StreamOutput(fopen('php://stdout', 'w')),
]))->run();
