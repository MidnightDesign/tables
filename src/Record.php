<?php

declare(strict_types=1);

namespace Midnight\Table;

interface Record
{
    public function field(int | string $index): mixed;
}
