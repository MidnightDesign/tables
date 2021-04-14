<?php

declare(strict_types=1);

namespace Midnight\Table\Test;

use LogicException;

final class TestUtil
{
    /**
     * @template K of array-key
     * @template V
     * @param iterable<K, V> $items
     * @return array<K, V>
     */
    public static function toArray(iterable $items): array
    {
        /** @var array<K, V> $array */
        $array = [];
        foreach ($items as $key => $item) {
            $array[$key] = $item;
        }
        return $array;
    }

    /**
     * @template T
     * @param iterable<array-key, T> $items
     * @psalm-return T
     */
    public static function first(iterable $items)
    {
        foreach ($items as $item) {
            return $item;
        }
        throw new LogicException('Iterable is empty');
    }
}
