<?php

declare(strict_types=1);

namespace Midnight\Table\Test;

use LogicException;
use Midnight\Table\InMemoryTable;
use PHPUnit\Framework\TestCase;

final class InMemoryTableTest extends TestCase
{
    public function testRecordsFromOneDimensionalArray(): void
    {
        $table = InMemoryTable::fromOneDimensionalArray(['foo', 'bar', 'baz', 'qux', 'meh', 'xxx'], 3);

        $records = TestUtil::toArray($table->records());

        self::assertCount(2, $records);
        self::assertSame('foo', $records[0]->field(0));
        self::assertSame('bar', $records[0]->field(1));
        self::assertSame('baz', $records[0]->field(2));
        self::assertSame('qux', $records[1]->field(0));
        self::assertSame('meh', $records[1]->field(1));
        self::assertSame('xxx', $records[1]->field(2));
    }

    public function testColumnFromOneDimensionalArrayByIndex(): void
    {
        $table = InMemoryTable::fromOneDimensionalArray(['foo', 'bar', 'baz', 'qux'], 2);

        $firstColumn = TestUtil::toArray($table->column(0));
        $secondColumn = TestUtil::toArray($table->column(1));

        self::assertCount(2, $firstColumn);
        self::assertCount(2, $secondColumn);
        self::assertSame('foo', $firstColumn[0]);
        self::assertSame('baz', $firstColumn[1]);
        self::assertSame('bar', $secondColumn[0]);
        self::assertSame('qux', $secondColumn[1]);
    }

    public function testColumnFromOneDimensionalArrayByColumnName(): void
    {
        $table = InMemoryTable::fromOneDimensionalArray(['foo', 'bar', 'baz', 'qux'], ['A', 'B']);

        $columnA = TestUtil::toArray($table->column('A'));
        $columnB = TestUtil::toArray($table->column('B'));

        self::assertCount(2, $columnA);
        self::assertCount(2, $columnB);
        self::assertSame('foo', $columnA[0]);
        self::assertSame('baz', $columnA[1]);
        self::assertSame('bar', $columnB[0]);
        self::assertSame('qux', $columnB[1]);
    }

    public function testAccessRecordFieldsByColumnName(): void
    {
        $table = InMemoryTable::fromOneDimensionalArray(['foo', 'bar'], ['A', 'B']);

        $record = TestUtil::toArray($table->records())[0];

        self::assertSame('foo', $record->field('A'));
        self::assertSame('bar', $record->field('B'));
    }

    public function testColumnNamesReturnsNullIfNoNamesWereProvided(): void
    {
        $table = InMemoryTable::fromOneDimensionalArray(['foo', 'bar'], 2);

        self::assertNull($table->columnNames());
    }

    public function testColumnNamesFromOneDimensionalArray(): void
    {
        $table = InMemoryTable::fromOneDimensionalArray(['foo', 'bar'], ['A', 'B']);

        self::assertSame(['A', 'B'], $table->columnNames());
    }

    public function testThrowsAnExceptionWhenTryingToGetAColumnByNameIfNoNamesWereGiven(): void
    {
        $table = InMemoryTable::fromOneDimensionalArray(['foo', 'bar'], 2);

        $this->expectException(LogicException::class);

        TestUtil::toArray($table->column('A'));
    }
}
