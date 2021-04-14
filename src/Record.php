<?php

declare(strict_types=1);

namespace Midnight\Table;

/**
 * @template T
 */
interface Record
{
    /**
     * @return T
     */
    public function field(int | string $index): mixed;
}
