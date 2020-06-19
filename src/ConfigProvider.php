<?php

declare(strict_types=1);

namespace Token;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                \Token\Handler\FileHandler::class => \Token\Handler\FileHandlerFactory::class,
                \Token\Handler\RedisHandler::class => \Token\Handler\RedisHandlerFactory::class,
                \Token\Contract\TokenInterface::class => \Token\TokenProxy::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config of token.',
                    'source' => __DIR__ . '/../publish/token.php',
                    'destination' => BASE_PATH . '/config/autoload/token.php',
                ],
            ],
        ];
    }
}
