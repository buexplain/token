<?php

declare(strict_types=1);

namespace TokenTest;

use Hyperf\Utils\Str;
use PHPUnit\Framework\TestCase;
use Token\Handler\RedisHandler;
use Token\Name;
use Token\Token;
use TokenTest\Stub\RedisStub;

class TokenTest extends TestCase
{
    /**
     * 测试客户端传递坏的token
     */
    public function testNameValidity()
    {
        $expire = 100;
        $length = 32;
        $r = new RedisStub();
        $h = new RedisHandler($r);
        //客户端没有传递token
        $t = new Token($h, $expire, $length);
        $this->assertFalse($t->load());
        $this->assertFalse($t->isValid());
        //客户端传递了空token
        $t = new Token($h, $expire, $length, '');
        $this->assertFalse($t->load());
        $this->assertFalse($t->isValid());
        //客户端传递了长度不符合的token
        $t = new Token($h, $expire, $length,'xxx');
        $this->assertFalse($t->load());
        $this->assertFalse($t->isValid());
        $t = new Token($h, $expire, $length, Str::random($length));
        $this->assertFalse($t->load());
        $this->assertFalse($t->isValid());
        //客户端传递了长度符合的token
        $t = new Token($h, $expire, $length,Str::random($length).'1');
        $this->assertFalse($t->load());
        $this->assertFalse($t->isValid());
        //客户端传递了格式不合法的token
        $t = new Token($h, $expire, $length,Str::random($length).'-1');
        $this->assertFalse($t->load());
        $this->assertFalse($t->isValid());
    }

    /**
     * 测试客户端传递正常的token
     */
    public function testNameNormal()
    {
        $expire = 100;
        $length = 32;
        $id = 'user';
        $r = new RedisStub();
        $h = new RedisHandler($r);
        $n = new Name($id, $expire, $length);
        $n->set('设备', 'pc');
        $data = [
            '_n' => [
                $n,
            ]
        ];
        $h->write($id, serialize($data), $expire);
        $t = new Token($h, $expire, $length, $n->__toString());
        $this->assertTrue($t->load());
        $this->assertTrue($t->isValid());
        $this->assertFalse($t->isChange());
        $this->assertTrue($t->getName()->expire() == time() + $expire);
        $this->assertTrue($n->get('设备') == $t->getName()->get('设备'));
        $this->assertTrue($t->getName()->__toString() == $n->__toString());
        $t->getName()->refresh();
        $this->assertTrue($t->getName()->__toString() != $n->__toString());
        $this->assertTrue($t->isChange());
    }

    /**
     * 测试无法序列化的数据
     */
    public function testNameSerializeFailed()
    {
        $expire = 100;
        $length = 32;
        $id = 'user';
        $r = new RedisStub();
        $h = new RedisHandler($r);
        $n = new Name($id, $expire, $length);
        $data = [
            '_n' => [
                $n,
            ]
        ];
        $h->write($id, 'sundries'.serialize($data), $expire);
        $t = new Token($h, $expire, $length, $n->__toString());
        $this->assertFalse($t->load());
        $this->assertFalse($t->isValid());
        $this->assertFalse($t->isChange());
        $h->delete($id);
        $t = new Token($h, $expire, $length, $n->__toString());
        $this->assertFalse($t->load());
        $this->assertFalse($t->isValid());
        $this->assertFalse($t->isChange());
        $data = [
            '_n' => []
        ];
        $h->write($id, serialize($data), $expire);
        $t = new Token($h, $expire, $length, $n->__toString());
        $this->assertFalse($t->load());
        $this->assertFalse($t->isValid());
        $this->assertFalse($t->isChange());
    }

    /**
     * 测试过期的数据
     */
    public function testNameExpired()
    {
        $expire = 100;
        $length = 32;
        $id = 'user';
        $r = new RedisStub();
        $h = new RedisHandler($r);
        $n = new Name($id, -100, $length);
        $data = [
            '_n' => [
                $n,
            ]
        ];
        $h->write($id, serialize($data), $expire);
        $t = new Token($h, $expire, $length, $n->__toString());
        $this->assertFalse($t->load());
        $this->assertFalse($t->isValid());
        $this->assertFalse($t->isChange());
    }

    /**
     * 多设备登录测试
     */
    public function testMultiDeviceLogin()
    {
        $expire = 100;
        $length = 32;
        $id = 'user';
        $r = new RedisStub();
        $h = new RedisHandler($r);
        //登录pc
        $t = new Token($h, $expire, $length);
        $t->setId($id);
        $t->getName()->set('device', 'pc');
        $t->save();
        //检查pc是否已经登录
        $t = new Token($h, $expire, $length);
        $t->setId($id);
        $this->assertTrue($t->load());
        $names = $t->getNames();
        foreach ($names as $name) {
            $this->assertTrue($name->get('device') == 'pc');
        }
        $this->assertFalse($t->isChange());
        //登录app
        $t = new Token($h, $expire, $length);
        $t->setId($id);
        $t->getName()->set('device', 'app');
        $t->save();
        //检查pc和app是否已经登录
        $t = new Token($h, $expire, $length);
        $t->setId($id);
        $this->assertTrue($t->load());
        $names = $t->getNames();
        foreach ($names as $name) {
            $this->assertTrue($name->get('device') == 'pc' || $name->get('device') == 'app');
        }
        $this->assertFalse($t->isChange());
    }
}