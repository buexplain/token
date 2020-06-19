<?php

declare(strict_types=1);

namespace Token\Exception;

use Exception;
use Throwable;

class UnauthorizedException extends Exception
{
    public function __construct($message = '请求未授权', $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
