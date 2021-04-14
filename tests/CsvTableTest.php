<?php

declare(strict_types=1);

namespace Midnight\Table\Test;

use LogicException;
use Midnight\Table\CsvTable;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

final class CsvTableTest extends TestCase
{
    private vfsStreamDirectory $fs;

    public function testRecordsDoesNotReturnTheHeaderRow(): void
    {
        $fileName = $this->createCsv("Col A,Col B\nVal A,Val B");

        $records = TestUtil::toArray(CsvTable::withHeader($fileName)->records());

        self::assertCount(1, $records);
    }

    public function testRecordsReturnsAll(): void
    {
        $fileName = $this->createCsv("Col A,Col B\nVal A,Val B");

        $records = TestUtil::toArray(CsvTable::withoutHeader($fileName)->records());

        self::assertCount(2, $records);
    }

    public function testColumnByIndex(): void
    {
        $fileName = $this->createCsv("Col A,Col B\nVal A,Val B");

        $fields = TestUtil::toArray(CsvTable::withoutHeader($fileName)->column(1));

        self::assertCount(2, $fields);
        self::assertSame('Col B', $fields[0]);
        self::assertSame('Val B', $fields[1]);
    }

    public function testColumnByName(): void
    {
        $fileName = $this->createCsv("Col A,Col B\nVal 1A,Val 1B\nVal 2A,Val 2B");

        $fields = TestUtil::toArray(CsvTable::withHeader($fileName)->column('Col B'));

        self::assertCount(2, $fields);
        self::assertSame('Val 1B', $fields[0]);
        self::assertSame('Val 2B', $fields[1]);
    }

    public function testColumnByNameFromATableWithoutAHeader(): void
    {
        $fileName = $this->createCsv("Val A,Val B");
        $table = CsvTable::withoutHeader($fileName);

        $this->expectException(LogicException::class);

        $table->column('Column X');
    }

    public function testColumnByNameFromAnEmptyFile(): void
    {
        $fileName = $this->createCsv('');
        $table = CsvTable::withoutHeader($fileName);

        $this->expectException(LogicException::class);

        $table->column('Column X');
    }

    public function testRecordFieldsByIndex(): void
    {
        $fileName = $this->createCsv('One,Two');
        $table = CsvTable::withoutHeader($fileName);

        $record = TestUtil::first($table->records());

        self::assertSame('One', $record->field(0));
        self::assertSame('Two', $record->field(1));
    }

    public function testRecordFieldsByName(): void
    {
        $fileName = $this->createCsv("A,B\nOne,Two");
        $table = CsvTable::withHeader($fileName);

        $record = TestUtil::first($table->records());

        self::assertSame('One', $record->field('A'));
        self::assertSame('Two', $record->field('B'));
    }

    public function testAccessRecordFieldWithoutColumnNamesByName(): void
    {
        $fileName = $this->createCsv("One,Two");
        $table = CsvTable::withoutHeader($fileName);
        $record = TestUtil::first($table->records());

        $this->expectException(LogicException::class);

        $record->field('A');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = vfsStream::setup();
    }

    private function createCsv(string $content): string
    {
        $fileName = $this->fs->url() . '/test.csv';
        \Safe\file_put_contents($fileName, $content);
        return $fileName;
    }
}
