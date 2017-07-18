<?php
// declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use src\Services\HtmlFormatter;

/**
 * @covers HtmlFormatter
 */
final class HtmlFormatterTest extends TestCase
{
    public function testCanBeCreatedFromValidFile()
    {
        // $html = new HtmlFormatter(__DIR__ . '/files/file1.htm');
        $this->assertInstanceOf(HtmlFormatter::class, HtmlFormatter::fromFile(__DIR__ . '/files/file1.htm'));
    }

    public function testCannotBeCreatedFromInvalidFile(): void
    {
        // $this->expectException(InvalidArgumentException::class);

        // Email::fromString('invalid');
    }

    public function testCanBeUsedAsString(): void
    {
        // $this->assertEquals(
        //     'user@example.com',
        //     Email::fromString('user@example.com')
        // );
    }
}
