<?php

declare(strict_types=1);

namespace Midnight\Table;

interface Table
{
    /**
     * @return iterable<int, Record>
     */
    public function records(): iterable;

    /**
     * @return iterable<int, mixed>
     */
    public function column(int | string $index): iterable;

    /**
     * @return list<string>|null
     */
    public function columnNames(): ?array;
}
