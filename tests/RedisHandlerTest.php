<?php

declare(strict_types=1);

namespace TokenTest;

use PHPUnit\Framework\TestCase;
use Token\Handler\FileHandler;
use Token\Handler\RedisHandler;
use TokenTest\Stub\RedisStub;

class RedisHandlerTest extends TestCase
{
    /**
     * @var RedisStub
     */
    protected static $r;
    /**
     * @var FileHandler
     */
    protected static $h;

    protected static $prefix = 'token:';

    public static function setUpBeforeClass()
    {
        self::$r = new RedisStub();
        self::$h = new RedisHandler(self::$r);
    }

    public function testWriteAndReadAndDelete()
    {
        $id = '1991';
        $data = '100';
        self::$h->write($id, $data, 10);
        $this->assertTrue(isset(self::$r->data[self::$prefix.$id]));
        $content = self::$h->read($id);
        $this->assertTrue($data == $content);
        self::$h->delete($id);
        $this->assertFalse(isset(self::$r->data[self::$prefix.$id]));
    }

    public static function tearDownAfterClass()
    {
        self::$h = null;
    }
}