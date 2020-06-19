<?php

declare(strict_types=1);

namespace TokenTest\Stub;

use Hyperf\Redis\Redis as RedisProxy;

class RedisStub extends RedisProxy
{
    public $data = [];

    public function __construct()
    {
    }

    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'get':
                return isset($this->data[$arguments[0]]) ? $this->data[$arguments[0]] : null;
                break;
            case 'setEx':
                $this->data[$arguments[0]] = $arguments[2];
                break;
            case 'del':
                unset($this->data[$arguments[0]]);
                break;
        }
        return null;
    }
}