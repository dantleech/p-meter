<?php

namespace PMeter\Transform;

use Generator;
use PMeter\Util\StringUtil;

class AnsiRedrawTransform
{
    const ANSI_SAVE_CURSOR_POS = "\033[s";
    const ANSI_RESTORE_CURSOR_POS = "\033[u";

    public function __invoke(): Generator
    {
        $data = yield;
        $lastData = null;
        $lineLength = 0;

        while (true) {
            if (null === $lastData) {
                $data = self::ANSI_SAVE_CURSOR_POS . $data;
            }

            if ($lastData) {
                $data = self::ANSI_RESTORE_CURSOR_POS . $data;

                $lineLength = $this->maxLineLength($data, $lineLength);
                $data = $this->maximizeLines($data, $lineLength);
            }

            $data = yield $data;
            $lastData = $data;
        }
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
        $result = trim($result, PHP_EOL);
        $lines = explode(PHP_EOL, $result);
        foreach ($lines as &$line) {
            $line = StringUtil::pad($line, $maxLineLength);
        }

        return implode(PHP_EOL, $lines);
    }
}
