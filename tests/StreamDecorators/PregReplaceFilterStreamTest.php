<?php
namespace ZBateson\StreamDecorators;

use LegacyPHPUnit\TestCase;
use GuzzleHttp\Psr7;
use RuntimeException;

/**
 * Description of PregReplaceFilterStreamTest
 *
 * @group PregReplaceFilterStream
 * @covers ZBateson\StreamDecorators\PregReplaceFilterStream
 * @author Zaahid Bateson
 */
class PregReplaceFilterStreamTest extends TestCase
{
    public function testRead()
    {
        $stream = Psr7\Utils::streamFor('a-ll t-h-e k-ing\'s me-n');
        $test = new PregReplaceFilterStream($stream, '/\-/', '');
        $this->assertSame('all the king\'s men', $test->getContents());
    }

    public function testReadBuffered()
    {
        $str = str_repeat('All the King\'s Men ', 8000);
        $filter = str_repeat('A-l-l t-h-e K-in-g\'s M-en ', 8000);
        $stream = Psr7\Utils::streamFor($filter);

        $test = new PregReplaceFilterStream($stream, '/\-/', '');
        for ($i = 0; $i < strlen($str); $i += 10) {
            $this->assertSame(substr($str, $i, 10), $test->read(10));
        }
    }

    public function testSeekUnsopported()
    {
        $stream = Psr7\Utils::streamFor('a-ll t-h-e k-ing\'s me-n');
        $test = new PregReplaceFilterStream($stream, '/\-/', '');
        $this->assertFalse($test->isSeekable());
        $exceptionThrown = false;
        try {
            $test->seek(0);
        } catch (RuntimeException $exc) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown);
    }
}
