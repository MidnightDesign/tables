<?php

declare(strict_types=1);

namespace Midnight\Table;

use LogicException;

use function count;
use function is_array;
use function is_int;

/**
 * @template T
 * @implements Table<T>
 */
final class InMemoryTable implements Table
{
    /** @var list<string>|null */
    private ?array $columnNames = null;
    /** @var array<string, int>|null */
    private ?array $columnIndexes = null;

    /**
     * @param list<T> $data
     * @param int $columns
     * @var list<T> $data
     * @var int $columns
     */
    private function __construct(private array $data, private int $columns)
    {
    }

    /**
     * @template TData
     * @param list<TData> $data
     * @param int|list<string> $columns
     * @return self<TData>
     */
    public static function fromOneDimensionalArray(
        array $data,
        int | array $columns
    ): self {
        $columnNames = null;
        if (is_array($columns)) {
            $columnNames = $columns;
            $columns = count($columns);
        }
        $table = new self($data, $columns);
        if ($columnNames !== null) {
            $table->columnNames = $columnNames;
        }
        return $table;
    }

    /**
     * @return iterable<int, Record<T>>
     */
    public function records(): iterable
    {
        $recordData = [];
        foreach ($this->data as $index => $value) {
            $recordData[] = $value;
            $i = ($index + 1) % $this->columns;
            if ($i !== 0) {
                continue;
            }
            yield new ArrayRecord($recordData, $this->columnNames);
            $recordData = [];
        }
    }

    /**
     * @return iterable<int, T>
     */
    public function column(int | string $index): iterable
    {
        $index = $this->columnIndex($index);
        $numberOfDataPoints = count($this->data);
        $i = $index;
        while (true) {
            if ($i >= $numberOfDataPoints) {
                break;
            }
            yield $this->data[$i];
            $i += $this->columns;
        }
    }

    /**
     * @return list<string>|null
     */
    public function columnNames(): ?array
    {
        return $this->columnNames;
    }

    private function columnIndex(int | string $index): int
    {
        if (is_int($index)) {
            return $index;
        }
        if ($this->columnIndexes === null) {
            if ($this->columnNames === null) {
                throw new LogicException('This table has no column names');
            }
            /** @var array<string, int> $columnIndexes */
            $columnIndexes = \Safe\array_flip($this->columnNames);
            $this->columnIndexes = $columnIndexes;
        }
        return $this->columnIndexes[$index];
    }
}
