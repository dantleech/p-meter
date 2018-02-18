<?php

namespace PMeter\Transform;

use MathPHP\Statistics\Average;
use MathPHP\Statistics\Descriptive;
use Generator;
use InvalidArgumentException;

class SummaryTransform
{
    /**
     * @var array
     */
    private $groupBy;

    /**
     * @var array
     */
    private $summarizeFields;

    public function __construct(array $groupBy = [], array $summarizeFields = [])
    {
        $this->groupBy = $groupBy;
        $this->summarizeFields = $summarizeFields;
    }

    public function __invoke(): Generator
    {
        $data = yield;
        $collection = [];

        while (true) {
            if (false === is_array($data)) {
                throw new InvalidArgumentException(sprintf(
                    '%s must be passed an array input, got "%s"',
                    __CLASS__, gettype($data)
                ));
            }

            // TODO: This is a massive memory leak
            $collection[] = (array) $data;
            $grouped = $this->groupData($collection);
            $summarized = $this->summarizeData($grouped);

            $data = yield $summarized;
        }
    }

    private function groupData(array $collection)
    {
        if (empty($this->groupBy)) {
            return $collection;
        }

        $grouped = [];
        foreach ($collection as $row) {
            $row = (array) $row;
            $hash = [];

            foreach ($this->groupBy as $groupBy) {
                if (!isset($row[$groupBy])) {
                    throw new InvalidArgumentException(sprintf(
                        'Group by field "%s" does not exist in input with fields "%s"',
                        $groupBy, implode('", "', array_keys($row))
                    ));
                }
                $hash[] = $row[$groupBy];
            }

            $hash = implode(', ', $hash);

            if (!isset($grouped[$hash])) {
                $grouped[$hash] = [];
            }

            $grouped[$hash][] = $row;
        }

        return $grouped;
    }

    private function summarizeData($collection)
    {
        return array_map(function ($table){
            $nbSamples = count($table);
            $fieldValues = [];
            $row = [];
            foreach ($this->summarizeFields as $summaryField) {
                foreach ($table as $row) {
                    if (false === isset($fieldValues[$summaryField])) {
                        $fieldValues[$summaryField] = [];
                    }

                    $fieldValues[$summaryField][] = $row[$summaryField];

                    unset($row[$summaryField]);
                }
            }

            $summary = [
                'samples' => $nbSamples,
            ];
            foreach ($fieldValues as $field => $values) {
                $summary = array_merge($row, $summary, [
                    $field . '-mean' => Average::mean($values),
                    $field . '-min' => min($values),
                    $field . '-max' => max($values),
                    $field . '-stdev' => Descriptive::standardDeviation($values, false),
                ]);
            }

            return $summary;
        }, $collection);
    }
}
