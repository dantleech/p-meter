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
        $aggregates = [];
        $count = 0;

        while (true) {
            if (false === is_array($data)) {
                throw new InvalidArgumentException(sprintf(
                    '%s must be passed an array input, got "%s"',
                    __CLASS__, gettype($data)
                ));
            }

            $count++;

            $hash = $this->buildHash($data);

            if (!isset($aggregates[$hash])) {
                $aggregates[$hash] = [];
            }

            foreach ($this->summarizeFields as $field) {
                if (false === isset($aggregates[$hash][$field])) {
                    $aggregates[$hash][$field] = [];
                }

                $aggregates[$hash][$field][] = $data[$field];
            }

            $data = yield $this->summarizeData($aggregates, $count);
        }
    }

    private function summarizeData(array $aggregates, int $count): array
    {
        $summary = [];
        foreach ($aggregates as $hash => $fieldValues) {
            $nbSamples = null;
            $summary[$hash]['hash'] = $hash;
            foreach ($fieldValues as $field => $values) {
                if (null === $nbSamples) {
                    $nbSamples = count($values);
                    $summary[$hash]['samples'] = $nbSamples;
                }
                $summary[$hash] = array_merge($summary[$hash], [
                    $field . '-mean' => Average::mean($values),
                    $field . '-min' => min($values),
                    $field . '-max' => max($values),
                ]);
            }

        }

        return $summary;
    }

    private function buildHash(array $row): string
    {
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

        return implode(', ', $hash);
    }
}
