<?php

declare(strict_types=1);

namespace Token\Handler;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Filesystem\Filesystem;
use Psr\Container\ContainerInterface;
use Token\Exception\InvalidConfigException;

class FileHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $path = $config->get('token.options.path');
        if (! $path) {
            throw new InvalidConfigException('Invalid token path.');
        }
        return new FileHandler($container->get(Filesystem::class), $path);
    }
}
