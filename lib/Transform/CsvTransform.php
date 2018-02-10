<?php

namespace PMeter\Transform;

use Generator;

class CsvTransform
{
    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var bool
     */
    private $header;

    public function __construct(string $delimiter = ',', bool $header = false)
    {
        $this->delimiter = $delimiter;
        $this->header = $header;
    }

    public function __invoke(): Generator
    {
        $data = yield;
        $first = true;

        while (true) {
            $line = $this->encode($data);

            if ($first && $this->header) {
                $line = $this->encode(array_keys($data)) . $line;
                $first = false;
            }

            $data = yield $line;
        }
    }

    private function encode(array $data)
    {
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, $this->convertToScalarValues($data), $this->delimiter);
        rewind($handle);
        $data = stream_get_contents($handle);
        fclose($handle);

        return $data;
    }

    private function convertToScalarValues(array $data)
    {
        return array_map(function ($value) {
            if (is_scalar($value)) {
                return $value;
            }

            return '<' . gettype($value) . '>';
        }, $data);
    }
}
