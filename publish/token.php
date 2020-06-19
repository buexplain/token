<?php

declare(strict_types=1);

return [
    'handler' => \Token\Handler\RedisHandler ::class,
    'options' => [
        //此处的connection对应的正是config/autoload/redis.php配置的key值
        'connection' => 'default',
        //redis前缀
        'prefix' => 'token:',
        //文件驱动的存储路径
        'path' => BASE_PATH . '/runtime/token',
        //客户端传递token时的key值
        'name' => 'Authorization',
        //从客户端获取token的方式
        'access' => \Token\TokenManager::ACCESS_HEADER|\Token\TokenManager::ACCESS_QUERY|\Token\TokenManager::ACCESS_COOKIE,
        //过期时间，单位秒
        'expire' => 86400,
        //token随机字符串的长度
        'length' => 32,
    ],
];