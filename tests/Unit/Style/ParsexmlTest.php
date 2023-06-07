<?php
declare(strict_types=1);
namespace Sypets\OsbibxParser\Tests\Unit\Style;

use PHPUnit\Framework\TestCase;
use Sypets\OsbibxParser\Style\Parsexml;
use variant;

final class ParsexmlTest extends TestCase
{
    /**
     * @todo Parsestyle currently works only with XML files without newlines. For that reason we are using a file
     *   without newlines in Fixtures/Xml/style.xml. Change this, reformat the file and run tests again.
     */
    public function testExtractEntriesReturnCorrectNumberOfElements(): void
    {
        $subject = new Parsexml();
        $styleFile = __DIR__ . '/Fixtures/Xml/style.xml';
        $fh = fopen($styleFile, 'r');
        $entries = $subject->extractEntries($fh);
        fclose($fh);
        $this->assertSame(5, count($entries));
    }

    /**
     * @todo Parsestyle currently works only with XML files without newlines. For that reason we are using a file
     *   without newlines in Fixtures/Xml/style.xml. Change this, reformat the file and run tests again.
     */
    public function testExtractEntriesFromFileReturnCorrectNumberOfElements(): void
    {
        $subject = new Parsexml();
        $styleFile = __DIR__ . '/Fixtures/Xml/style.xml';
        $entries = $subject->extractEntriesFromFile($styleFile);
        $this->assertSame(5, count($entries));
        var_dump($entries);
    }


}
