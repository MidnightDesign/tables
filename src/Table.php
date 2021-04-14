<?php

declare(strict_types=1);

namespace Midnight\Table;

/**
 * @template T
 */
interface Table
{
    /**
     * @return iterable<int, Record<T>>
     */
    public function records(): iterable;

    /**
     * @return iterable<int, T>
     */
    public function column(int | string $index): iterable;

    /**
     * @return list<string>|null
     */
    public function columnNames(): ?array;
}
