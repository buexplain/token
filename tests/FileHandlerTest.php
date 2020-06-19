<?php

declare(strict_types=1);

namespace TokenTest;

use Hyperf\Utils\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Token\Handler\FileHandler;

class FileHandlerTest extends TestCase
{
    /**
     * @var string
     */
    protected static $path;

    /**
     * @var Filesystem
     */
    protected static $f;

    /**
     * @var FileHandler
     */
    protected static $h;

    public static function setUpBeforeClass()
    {
        self::$path = __DIR__.'/tmp';
        self::$f = new Filesystem();
        self::$h = new FileHandler(self::$f, self::$path);
    }

    public function testWriteAndReadAndDelete()
    {
        $id = '1991';
        $data = '100';
        self::$h->write($id, $data, 10);
        $this->assertTrue(self::$f->isFile(self::$path.DIRECTORY_SEPARATOR.$id));
        $content = self::$h->read($id);
        $this->assertTrue($data == $content);
        self::$h->delete($id);
        $this->assertFalse(self::$f->isFile(self::$path.DIRECTORY_SEPARATOR.$id));
    }

    public static function tearDownAfterClass()
    {
        self::$f->deleteDirectory(self::$path);
        @rmdir(self::$path);
    }
}