<?php

declare(strict_types=1);

namespace Token\Handler;

use Token\Contract\TokenHandlerInterface;
use Hyperf\Redis\Redis as RedisProxy;
use Token\Exception\InvalidConfigException;

class RedisHandler implements TokenHandlerInterface
{
    /**
     * @var \Hyperf\Redis\Redis|\Predis\Client|\Redis|\RedisArray|\RedisCluster
     */
    protected $redis;

    /**
     * @var string
     */
    protected $prefix = 'token:';

    public function __construct($redis, string $prefix='token:')
    {
        if (! $redis instanceof \Redis && ! $redis instanceof \RedisArray && ! $redis instanceof \RedisCluster && ! $redis instanceof \Predis\Client && ! $redis instanceof RedisProxy) {
            throw new InvalidConfigException(sprintf('%s() expects parameter 1 to be Redis, RedisArray, RedisCluster, Predis\Client or Hyperf\Redis\Redis, %s given', __METHOD__, \is_object($redis) ? \get_class($redis) : \gettype($redis)));
        }

        $this->redis = $redis;
        $this->prefix = $prefix;
    }

    public function read(string $id)
    {
        return $this->redis->get($this->prefix.$id) ?: '';
    }

    public function write(string $id, string $data, int $expire)
    {
        return (bool) $this->redis->setEx($this->prefix.$id, $expire, $data);
    }

    public function delete(string $id)
    {
        $this->redis->del($this->prefix.$id);
        return true;
    }
}
