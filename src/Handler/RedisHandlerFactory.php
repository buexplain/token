<?php

declare(strict_types=1);

namespace Token\Handler;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Redis\RedisFactory;
use Psr\Container\ContainerInterface;

class RedisHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $connection = $config->get('token.options.connection');
        $prefix = $config->get('token.options.prefix', 'token:');
        $redisFactory = $container->get(RedisFactory::class);
        $redis = $redisFactory->get($connection);
        return new RedisHandler($redis, $prefix);
    }
}
