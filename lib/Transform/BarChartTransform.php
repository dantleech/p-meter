<?php

namespace PMeter\Transform;

use Generator;
use PMeter\Util\StringUtil;
use IntlChar;

class BarChartTransform
{
    const PADDING = 1;

    /**
     * @var string
     */
    private $labelField;

    /**
     * @var string
     */
    private $valueField;

    /**
     * @var int
     */
    private $maxWidth;

    public function __construct(string $labelField, string $valueField, int $maxWidth = 50)
    {
        $this->labelField = $labelField;
        $this->valueField = $valueField;
        $this->maxWidth = $maxWidth;
    }

    public function __invoke(): Generator
    {
        $data = yield;

        while (true) {
            $data = yield $this->graph($data);
        }
    }

    private function graph(array $data)
    {
        $graph = [];
        $labelWidth = $this->maxLabelWidth($data);
        $maxValue = $this->maxValue($data);
        $barWidth = $this->barWidth($maxValue, $maxValue);

        foreach ($data as $row) {
            $graph[] = sprintf(
                '%-' . $labelWidth . 's |%s %s',
                $row[$this->labelField],
                StringUtil::pad($this->bar($row, $maxValue), $barWidth),
                $row[$this->valueField]
            );
        }

        return implode(PHP_EOL, $graph) . PHP_EOL;
    }

    private function maxLabelWidth(array $data): int
    {
        $max = 0;

        foreach ($data as $row) {
            $row = (array) $row;
            $length = mb_strlen($row[$this->labelField]);
            if ($length > $max) {
                $max = $length;
            }
        }

        return $max + self::PADDING;
    }

    private function barWidth(float $max, float $current)
    {
        if ($max == 0) {
            return $max;
        }

        return ceil(($current / $max) * $this->maxWidth);
    }

    private function maxValue(array $data)
    {
        $max = 0;
        foreach ($data as $row) {
            $value = $row[$this->valueField];

            if ($value > $max) {
                $max = $value;
            }
        }

        return $max;
    }

    private function bar($row, $maxValue)
    {
        if ($maxValue == 0) {
            return '';
        }

        // fill solid section
        $char = IntlChar::chr(0x2588);
        $value = $row[$this->valueField];
        $barWidth = $this->barWidth($maxValue, $value);
        $bar = '';

        if ($barWidth == 0) {
            return $bar;
        }

        // draw solid segments excepting the last one
        if ($barWidth > 1) {
            $bar .= str_repeat($char,  $barWidth - 1);
        }

        // determine final segments char
        $stepValue = $maxValue / $this->maxWidth;
        $remainderValue = $value - ($stepValue * floor($value / $stepValue)) ;

        // perfect fit, full segment
        if (0 == $remainderValue) {
            return $bar . $char;
        }

        $fraction = $remainderValue / $stepValue;
        $offset = (8 - ((int) floor(8 * $fraction))) % 8;

        // 0th offset is blank
        if ($offset === 0) {
            return $bar;
        }

        $char = hexdec(2588) + $offset;
        $bar .= IntlChar::chr($char);

        return $bar;
    }
}
