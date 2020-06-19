<?php

declare(strict_types=1);

namespace Token;

use Token\Contract\TokenHandlerInterface;
use Token\Contract\TokenInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Token\Exception\InvalidConfigException;

class TokenManager
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConfigInterface
     */
    protected $config;

    //从请求的header中获取token
    const ACCESS_HEADER = 0x00001;

    //从请求的query string中获取token
    const ACCESS_QUERY = 0x00002;

    //从请求的cookie中获取token
    const ACCESS_COOKIE = 0x00040;

    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        $this->container = $container;
        $this->config = $config;
        $handler = $this->config->get('token.handler');
        if (! $handler || ! class_exists($handler)) {
            throw new InvalidConfigException('Invalid handler of token');
        }
        if (!$this->config->has('token.options.name')) {
            throw new InvalidConfigException('Invalid name of token');
        }
        if (!$this->config->has('token.options.access')) {
            throw new InvalidConfigException('Invalid access of token');
        }
        if (!$this->config->has('token.options.expire')) {
            throw new InvalidConfigException('Invalid expire of token');
        }
        if (!$this->config->has('token.options.length')) {
            throw new InvalidConfigException('Invalid length of token');
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return TokenInterface
     */
    public function start(ServerRequestInterface $request): TokenInterface
    {
        $key = $this->config->get('token.options.name');
        $access = $this->config->get('token.options.access');
        $name = '';
        if (($access&TokenManager::ACCESS_HEADER) == self::ACCESS_HEADER) {
            $name = $request->getHeaderLine($key);
        }
        if ($name == '' && ($access&TokenManager::ACCESS_QUERY) == self::ACCESS_QUERY) {
            $params = $request->getCookieParams();
            if (isset($params[$key])) {
                $name = $params[$key];
            }
        }
        if ($name == '' && ($access&TokenManager::ACCESS_COOKIE) == self::ACCESS_COOKIE) {
            $params = $request->getCookieParams();
            if (isset($params[$key])) {
                $name = $params[$key];
            }
        }
        $token = new Token(
            $this->buildHandler(),
            $this->config->get('token.options.expire'),
            $this->config->get(
                'token.options.length',
                32
            ),
            $name
        );
        $token->load();
        Context::set(TokenInterface::class, $token);
        return $token;
    }

    public function end(TokenInterface $token): void
    {
        $token->save();
    }

    protected function buildHandler(): TokenHandlerInterface
    {
        return $this->container->get($this->config->get('token.handler'));
    }
}
