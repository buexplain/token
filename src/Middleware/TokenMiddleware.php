<?php

declare(strict_types=1);

namespace Token\Middleware;

use Token\TokenManager;
use Hyperf\Contract\ConfigInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * token初始化与落库中间件
 * Class TokenMiddleware
 * @package Token\Middleware
 */
class TokenMiddleware implements MiddlewareInterface
{
    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(TokenManager $tokenManager, ConfigInterface $config)
    {
        $this->tokenManager = $tokenManager;
        $this->config = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $this->tokenManager->start($request);
        $response = $handler->handle($request);
        $this->tokenManager->end($token);
        return $response;
    }
}
