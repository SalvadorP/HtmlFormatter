<?php
/**
 * HtmlFormatter Test class code.
 * 
 * @file
 * @category DomUtilsTest
 * @package  HtmlFormatterTest
 * @author   Salvador Perez <salvadorperezd@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/SalvadorP/HtmlFormatter
 */

use PHPUnit\Framework\TestCase;
use src\Services\HtmlFormatter;

/**
 * Class HtmlFormatterTest
 *
 * @category DomUtilsTest
 * @package  HtmlFormatterTest
 * @author   Salvador Perez <salvadorperezd@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/SalvadorP/HtmlFormatter
 * @covers   HtmlFormatter
 */
final class HtmlFormatterTest extends TestCase
{
    /**
     * Checks if the class is created using a correct file.
     *
     * @return void
     */
    public function testCanBeCreatedFromValidFile()
    {
        $this->assertInstanceOf(
            HtmlFormatter::class, 
            HtmlFormatter::fromFile(__DIR__ . '/files/file1.htm')
        );
    }

    /**
     * Checks if an exception is thrown when using an incorrect file.
     *
     * @return void
     */
    public function testCannotBeCreatedFromInvalidFile()
    {
        $this->expectException(InvalidArgumentException::class);
        HtmlFormatter::fromFile(__DIR__ . '/files/file1.docx');
    }

    /**
     * Checks if an exception is thrown when using an unexisting file.
     *
     * @return void
     */
    public function testCannotBeCreatedFromUnexistingFile()
    {
        $this->expectException(InvalidArgumentException::class);
        HtmlFormatter::fromFile(__DIR__ . '/files/file0.html');
    }

    /**
     * Checks if contains a text
     *
     * @return void
     */
    public function testContainsText()
    {
        $htmlText = '<p class="MsoNormal">With some text</p>';
        $html = HtmlFormatter::fromFile(__DIR__ . '/files/file1.htm');
        $this->assertContains(
            $htmlText,
            $html->processFileAndGetHTML()
        );
    }

    /**
     * Checks if there is any header 2
     *
     * @return void
     */
    public function testContainsHeader2()
    {
        $htmlText = '<h2>Just this</h2>';
        $html = HtmlFormatter::fromFile(__DIR__ . '/files/file1.htm');
        $this->assertContains(
            $htmlText,
            $html->processFileAndGetHTML()
        );        
    }

    /**
     * Checks if there is any header
     *
     * @return void
     */
    public function testContainsHeader3()
    {
        $htmlText = '<h3>Another tag</h3>';
        $html = HtmlFormatter::fromFile(__DIR__ . '/files/file1.htm');
        $this->assertContains(
            $htmlText,
            $html->processFileAndGetHTML()
        );        
    }
}
