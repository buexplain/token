<?php

declare(strict_types=1);

namespace TokenTest;

use PHPUnit\Framework\TestCase;
use TokenTest\Stub\AttributesStub;

class AttributesTest extends TestCase
{
    public function testIsChange()
    {
        $a = new AttributesStub();
        $this->assertTrue($a->isChange() == false);
    }

    public function testHas()
    {
        $a = new AttributesStub([1, 'a'=>1, 'c'=>['x'=>1]]);
        $this->assertTrue($a->has((string) 0));
        $this->assertTrue($a->has('a'));
        $this->assertFalse($a->has('c.x'));
        $this->assertFalse($a->has('b'));
        $this->assertFalse($a->isChange());
    }

    public function testGet()
    {
        $a = new AttributesStub([1, 'a'=>1, 'c'=>['x'=>1]]);
        $this->assertTrue($a->get((string) 0) == 1);
        $this->assertTrue($a->get('a') == 1);
        $this->assertTrue($a->get('c.x') == 1);
        $this->assertTrue($a->get('b') == null);
        $this->assertFalse($a->isChange());
    }

    public function testPull()
    {
        $data = [1, 'a'=>1, 'c'=>['x'=>1]];
        $a = new AttributesStub($data);
        $this->assertTrue($a->pull((string) 0) == 1);
        $this->assertTrue($a->pull('a') == 1);
        $this->assertTrue($a->pull('c.x') == 1);
        $this->assertTrue($a->pull('b') == null);
        $this->assertTrue($a->all() == ['c'=>[]]);
        $this->assertTrue($a->isChange());
    }

    public function testAll()
    {
        $data = [1, 'a'=>1, 'c'=>['x'=>1]];
        $a = new AttributesStub($data);
        $this->assertTrue($a->all() == $data);
        $this->assertFalse($a->isChange());
    }

    public function testSet()
    {
        $data = [1, 'a'=>1, 'c'=>['x'=>1]];
        $a = new AttributesStub();
        $a->set((string) 0, 1);
        $a->set('a', 1);
        $a->set('c.x', 1);
        $this->assertTrue($a->all() == $data);
        $this->assertTrue($a->isChange());
    }

    public function testForget()
    {
        $data = [1, 'a'=>1, 'c'=>['x'=>1], 'd'=>1];
        $a = new AttributesStub($data);
        $a->forget((string) 0);
        unset($data[0]);
        $this->assertTrue($a->all() == $data);
        $a->forget('a');
        unset($data['a']);
        $this->assertTrue($a->all() == $data);
        unset($data['c'], $data['d']);
        $a->forget(['c', 'd']);
        $this->assertTrue($a->all() == $data);
        $this->assertTrue($a->isChange());
    }

    public function testClear()
    {
        $a = new AttributesStub([1]);
        $a->clear();
        $this->assertTrue($a->all() == []);
        $this->assertTrue($a->isChange());
    }
}