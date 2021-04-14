<?php

declare(strict_types=1);

namespace Midnight\Table;

use LogicException;

use function array_flip;
use function is_string;

/**
 * @template T
 * @implements Record<T>
 */
final class ArrayRecord implements Record
{
    /** @var array<string, int>|null */
    private array | null $columnNameIndexes = null;

    /**
     * @param list<T> $data
     * @var list<T> $data
     * @param list<string>|null $columnNames
     */
    public function __construct(private array $data, ?array $columnNames = null)
    {
        if ($columnNames === null) {
            return;
        }

        $this->columnNameIndexes = array_flip($columnNames);
    }

    /**
     * @return T
     */
    public function field(int | string $index): mixed
    {
        if (is_string($index)) {
            if ($this->columnNameIndexes === null) {
                throw new LogicException('Can\'t access field by column name because this record has no column names.');
            }
            $index = $this->columnNameIndexes[$index];
        }
        return $this->data[$index];
    }
}
