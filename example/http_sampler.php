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

require __DIR__ . '/../vendor/autoload.php';

$result = (new Pipeline([
    new RotarySplitter([
        new HttpSampler('http://localhost:8080'),
        new HttpSampler('https://www.google.com'),
        new HttpSampler('http://www.wikipedia.org'),
    ]),
    new FilterTransform(['url', 'total_time']),
    //new CsvTransform(',', true),
    new SummaryTransform([ 'url' ], ['total_time']),
    new RotarySplitter([
        new BarChartTransform('hash', 'total_time-mean'),
        new TableTransform(),
    ]),
    new Batch(2),
    new ImplodeTransform(),
    new AnsiRedrawTransform(),
    new StreamOutput(fopen('php://stdout', 'w')),
]))->run();
