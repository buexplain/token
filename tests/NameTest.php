<?php

declare(strict_types=1);

namespace TokenTest;

use PHPUnit\Framework\TestCase;
use Token\Name;

class NameTest extends TestCase
{
    public function testRefresh()
    {
        $id = '1991';
        $expire = 100;
        $length = 18;
        $n = new Name($id, $expire, $length);
        $this->assertTrue($n->isChange());
        $s = $n->__toString();
        $this->assertTrue(strlen($s) == $length+strlen($id));
        $n->refresh();
        $this->assertTrue(strlen($n->__toString()) == $length+strlen($id));
        $this->assertTrue($n->__toString() != $s);
        $this->assertTrue($n->isChange());
    }

    public function testExpire()
    {
        $id = '1991';
        $expire = 100;
        $length = 18;
        $n = new Name($id, $expire, $length);
        $this->assertTrue($n->expire() == time() + $expire);
    }
}