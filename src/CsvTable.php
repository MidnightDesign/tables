<?php

declare(strict_types=1);

namespace Midnight\Table;

use LogicException;

use function array_flip;
use function assert;
use function fgetcsv;
use function is_array;
use function is_int;

final class CsvTable implements Table
{
    private bool $hasHeader = false;
    /** @var list<string>|null */
    private ?array $columnNames = null;
    /** @var array<string, int>|null */
    private ?array $columnNameIndexes = null;

    private function __construct(private string $fileName)
    {
    }

    public static function withoutHeader(string $fileName): self
    {
        return new self($fileName);
    }

    public static function withHeader(string $fileName): self
    {
        $table = new self($fileName);
        $table->hasHeader = true;
        return $table;
    }

    /**
     * @return iterable<int, Record>
     */
    public function records(): iterable
    {
        return $this->withFileHandle(
        /**
         * @param resource $handle
         * @return iterable<int, Record>
         */
            function ($handle): iterable {
                if ($this->hasHeader) {
                    fgetcsv($handle);
                }
                while (true) {
                    $row = fgetcsv($handle);
                    if ($row === false) {
                        break;
                    }
                    yield new ArrayRecord($row, $this->columnNames());
                }
            }
        );
    }

    /**
     * @return iterable<int, mixed>
     */
    public function column(int | string $index): iterable
    {
        $columnIndex = $this->columnIndex($index);
        return $this->withFileHandle(
        /**
         * @param resource $handle
         * @return iterable<int, mixed>
         */
            function ($handle) use ($columnIndex): iterable {
                if ($this->hasHeader) {
                    fgetcsv($handle);
                }
                while (true) {
                    $row = fgetcsv($handle);
                    if ($row === false) {
                        break;
                    }
                    yield $row[$columnIndex];
                }
            }
        );
    }

    /**
     * @return list<string>|null
     */
    public function columnNames(): ?array
    {
        if (!$this->hasHeader) {
            return null;
        }
        if ($this->columnNames === null) {
            $this->columnNames = $this->loadColumnNames();
        }
        return $this->columnNames;
    }

    /**
     * @template T
     * @param callable(resource): T $fn
     * @return T
     */
    private function withFileHandle(callable $fn): mixed
    {
        $fileHandle = \Safe\fopen($this->fileName, 'rb');
        return $fn($fileHandle);
    }

    /**
     * @return list<string>
     */
    private function loadColumnNames(): array
    {
        return $this->withFileHandle(
        /**
         * @param resource $handle
         * @return list<string>
         */
            function ($handle): array {
                $headers = fgetcsv($handle);
                assert(is_array($headers));
                return $headers;
            }
        );
    }

    private function columnIndex(int | string $index): int
    {
        if (is_int($index)) {
            return $index;
        }
        if (!$this->hasHeader) {
            throw new LogicException('This table has no headers');
        }
        return $this->columnNameIndexes()[$index];
    }

    /**
     * @return array<string, int>
     */
    private function columnNameIndexes(): array
    {
        if ($this->columnNameIndexes === null) {
            $columnNames = $this->columnNames();
            assert($columnNames !== null);
            $this->columnNameIndexes = array_flip($columnNames);
        }
        return $this->columnNameIndexes;
    }
}
