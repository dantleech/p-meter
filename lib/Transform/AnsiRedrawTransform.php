<?php

namespace PMeter\Transform;

use Generator;
use PMeter\Util\StringUtil;

class AnsiRedrawTransform
{
    const CLEAR_LINE = "\x1B[2K";
    const CURSOR_COL_ZERO = "\x1B[0G";

    public function __invoke(): Generator
    {
        $data = yield;
        $lastData = null;
        $lineLength = 0;

        while (true) {
            if ($lastData) {
                $lineLength = $this->maxLineLength($data, $lineLength);
                $data = $this->maximizeLines($data, $lineLength);
                $data = self::CLEAR_LINE . $data;
                $data = self::CURSOR_COL_ZERO . $data;
                $data = $this->resetYPosition($lastData, $data);
            }

            $data = yield $data;
            $lastData = $data;
        }
    }

    private function resetYPosition($lastResult, $result)
    {
        $lastHeight = substr_count($lastResult, PHP_EOL) - 1;

        return "\x1B[" . ($lastHeight) . 'A' . $result; // reset cursor Y pos
    }

    private function maxLineLength(string $result, int $maxLineLength)
    {
        foreach (explode(PHP_EOL, $result) as $line) {
            $length = mb_strlen($line);

            if ($length > $maxLineLength) {
                $maxLineLength = $length;
            }
        }

        return $maxLineLength;
    }

    private function maximizeLines(string $result, int $maxLineLength)
    {
        $lines = explode(PHP_EOL, $result);
        foreach ($lines as &$line) {
            $line = StringUtil::pad($line, $maxLineLength);
        }

        return implode(PHP_EOL, $lines);
    }
}
